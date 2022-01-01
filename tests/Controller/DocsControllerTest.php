<?php declare(strict_types=1);

namespace HarmBandstra\SwaggerUiBundle\Tests\Controller;

use HarmBandstra\SwaggerUiBundle\Controller\DocsController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class DocsControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testIndexRedirectsProperly(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/docs');

        $uri = $client->getInternalRequest()->getUri();
        $parsedUrl = parse_url($uri);

        $this->assertSame('/docs/', $parsedUrl['path']);
        $this->assertSame('url=/docs/file/petstore.json&configUrl=/docs/file/config.json', $parsedUrl['query']);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame(
            'Unable to load [Resources/public/index.html]. Did [ScriptHandler::linkAssets] run correctly?',
            $response->getContent()
        );
    }

    public function testIndexWithTrailingSlash(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRedirectWithValidFile(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/petstore.json');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testIfSwaggerFileReturnsPetstoreJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/file/petstore.json');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJsonString(
            file_get_contents(__DIR__ . '/../Resources/docs/petstore.json'),
            $response->getContent()
        );
    }

    public function testIfSwaggerFileReturnsPetstoreYaml(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/file/petstore.yaml');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJson($response->getContent());

        $yml = Yaml::parse(file_get_contents(__DIR__ . '/../Resources/docs/petstore.yaml'));
        $this->assertJsonStringEqualsJsonString(
            json_encode($yml),
            $response->getContent()
        );
    }

    public function testIfSwaggerFileReturnsErrorOnInvalidFile(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/file/invalid.yaml');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals(
            '"File [invalid.yaml] not defined under [hb_swagger_ui.files] in config.yml."',
            $response->getContent()
        );
    }

    public function testIfSwaggerFileReturnsErrorOnFileNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/file/not_found.yaml');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals(
            '"File [not_found.yaml] not found."',
            $response->getContent()
        );
    }

    public function testIfConfigFileCanBeLoaded(): void
    {
        $client = static::createClient();
        $client->request('GET', '/docs/file/config.json');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testIfConfigFileReturnsFileNotFoundWhenNoConfigFileIsSet(): void
    {
        $docsController = new DocsController(
            ["petstore.json", "petstore.yaml", "not_found.yaml"],
            __DIR__ . "../../tests/Resources/docs/",
            null,
            null
        );

        $response = $docsController->swaggerFileAction('config.json');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals(
            '"File [config.json] not defined under [hb_swagger_ui.files] in config.yml."',
            $response->getContent()
        );
    }
}
