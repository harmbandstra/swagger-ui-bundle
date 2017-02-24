<?php

namespace HarmBandstra\SwaggerUiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class DocsController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->forward(
            'HarmBandstraSwaggerUiBundle:Docs:redirect',
            ['fileName' => $this->getParameter('harm_bandstra_swagger_ui.default_file')]
        );
    }

    /**
     * @param Request $request
     * @param string $fileName
     *
     * @return RedirectResponse
     */
    public function redirectAction(Request $request, $fileName)
    {
        $docsDirectory = $this->getParameter('harm_bandstra_swagger_ui.directory');
        if (!is_dir(realpath($docsDirectory))) {
            throw new FileNotFoundException(sprintf('Directory [%s] not found.', $docsDirectory));
        }

        if (!is_file(realpath($docsDirectory . DIRECTORY_SEPARATOR . $fileName))) {
            throw new FileNotFoundException(sprintf('File [%s] not found.', $docsDirectory));
        }

        $swaggerUiRoute = sprintf('%s/bundles/harmbandstraswaggerui/swagger-ui/index.html', $request->getSchemeAndHttpHost());
        $swaggerFileRoute = $this->get('router')->generate('hb_swagger_ui_swagger_file', ['fileName' => $fileName]);

        return $this->redirect(
            sprintf('%s?url=%s/%s', $swaggerUiRoute, $swaggerFileRoute, $request->getSchemeAndHttpHost())
        );
    }

    /**
     * @param string $fileName
     *
     * @return JsonResponse
     */
    public function swaggerFileAction($fileName)
    {
        $filePath = realpath($this->getParameter('harm_bandstra_swagger_ui.directory') . DIRECTORY_SEPARATOR . pathinfo($fileName, PATHINFO_BASENAME));
        if (!is_file($filePath)) {
            throw new FileNotFoundException(sprintf('File [%s] not found.', $filePath));
        }

        $fileContents = file_get_contents($filePath);

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension === 'yml' || $extension === 'yaml') {
            $fileContents = Yaml::parse(file_get_contents($filePath));

            return new JsonResponse($fileContents);
        }

        return new JsonResponse($fileContents, Response::HTTP_OK, [], true);
    }
}
