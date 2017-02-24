<?php

namespace HarmBandstra\SwaggerUiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $docsDirectory = $this->getParameter('harm_bandstra_swagger_ui.directory');
        if (!is_dir(realpath($docsDirectory))) {
            throw new FileNotFoundException(sprintf('Directory [%s] not found.', $docsDirectory));
        }

        $defaultFile = $this->getParameter('harm_bandstra_swagger_ui.default_file');
        if (!is_file(realpath($docsDirectory . DIRECTORY_SEPARATOR . $defaultFile))) {
            throw new FileNotFoundException(sprintf('File [%s] not found.', $docsDirectory));
        }

        $swaggerUiRoute = sprintf('%s/bundles/harmbandstraswaggerui/swagger-ui/index.html', $request->getSchemeAndHttpHost());
        $swaggerFileRoute = $this->get('router')->generate('hb_swagger_ui_swagger_file', ['fileName' => $defaultFile]);

        return $this->redirect($swaggerUiRoute . '?url=' . $swaggerFileRoute);
    }

    /**
     * @param string $fileName
     *
     * @return JsonResponse
     */
    public function swaggerFileAction($fileName)
    {
        $filePath = realpath($this->getParameter('harm_bandstra_swagger_ui.directory') . DIRECTORY_SEPARATOR . $fileName);
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
