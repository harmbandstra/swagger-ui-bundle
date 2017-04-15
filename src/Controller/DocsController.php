<?php

namespace HarmBandstra\SwaggerUiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Yaml\Yaml;

class DocsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$request->get('url')) {
            // if there is no ?url=... parameter, redirect to the default one
            $specFiles = $this->getParameter('hb_swagger_ui.files');

            $defaultSpecFile = reset($specFiles);

            return $this->redirect($this->getRedirectUrlToSpec($request, $defaultSpecFile));
        }

        try {
            // check if public/index.html exists and get its path if it does
            $indexFilePath = $this->get('file_locator')->locate('@HBSwaggerUiBundle/Resources/public/index.html');
        } catch (\InvalidArgumentException $exception) {
            // index.html doesn't exist, let's update public/ with swagger-ui files
            $publicDir = $this->get('file_locator')->locate('@HBSwaggerUiBundle/Resources/public/');

            $swaggerDistDir = $this->getParameter('kernel.root_dir').'/../vendor/swagger-api/swagger-ui/dist';

            // update public dir
            $this->get('filesystem')->mirror($swaggerDistDir, $publicDir);

            // the public/index.html file should exists now, let's try to get its path again
            $indexFilePath = $this->get('file_locator')->locate('@HBSwaggerUiBundle/Resources/public/index.html');
        }

        return new Response(file_get_contents($indexFilePath));
    }

    /**
     * @param Request $request
     * @param string $fileName
     *
     * @return RedirectResponse
     */
    public function redirectAction(Request $request, $fileName)
    {
        $validFiles = $this->getParameter('hb_swagger_ui.files');

        // redirect to swagger file if that's what we're looking for
        if (in_array($fileName, $validFiles, true)) {
            return $this->redirect($this->getRedirectUrlToSpec($request, $fileName));
        }

        // redirect to the assets dir so that relative links work
        return $this->redirect('/bundles/hbswaggerui/'.$fileName);
    }

    /**
     * @param string $fileName
     *
     * @return JsonResponse
     */
    public function swaggerFileAction($fileName)
    {
        try {
            $filePath = $this->getFilePath($fileName);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension === 'yml' || $extension === 'yaml') {
            $fileContents = Yaml::parse(file_get_contents($filePath));

            return new JsonResponse($fileContents);
        }

        $fileContents = file_get_contents($filePath);

        return new JsonResponse($fileContents, Response::HTTP_OK, [], true);
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getFilePath($fileName = '')
    {
        $validFiles = $this->getParameter('hb_swagger_ui.files');

        if ($fileName !== '' && !in_array($fileName, $validFiles)) {
            throw new \RuntimeException(
                sprintf('File [%s] not defined under [hb_swagger_ui.files] in config.yml.', $fileName)
            );
        }

        $directory = $this->getParameter('hb_swagger_ui.directory');

        if ($directory === '') {
            throw new \RuntimeException(
                'Directory [hb_swagger_ui.directory] not defined or empty in config.yml.'
            );
        }

        $filePath = realpath($this->getParameter('hb_swagger_ui.directory') . DIRECTORY_SEPARATOR . $fileName);
        if (!is_file($filePath)) {
            throw new FileNotFoundException(sprintf('File [%s] not found.', $fileName));
        }

        return $filePath;
    }

    /**
     * @param Request $request
     * @param $fileName
     *
     * @return string
     */
    private function getRedirectUrlToSpec(Request $request, $fileName) {
        if (strpos($fileName, '/') === 0) {
            // if absolute path, point to it
            $specUrl = $request->getUriForPath($fileName);
        } elseif (preg_match('#http[s]?://#', $fileName)) {
            // if URL use it raw
            $specUrl = $fileName;
        } else {
            $specUrl = $this->generateUrl('hb_swagger_ui_swagger_file', ['fileName' => $fileName], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $this->generateUrl('hb_swagger_ui_default', ['url' => $specUrl]);
    }
}
