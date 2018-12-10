<?php

namespace HarmBandstra\SwaggerUiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class DocsControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/docs');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
    }

    public function testIndexWithTrailingSlash()
    {
        $client = static::createClient();
        $client->request('GET', '/docs/');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testRedirectWithValidFile()
    {
        $client = static::createClient();
        $client->request('GET', '/docs/petstore.json');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
    }

    public function testIfSwaggerFileReturnsPetstoreJson()
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

    public function testIfSwaggerFileReturnsPetstoreYaml()
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

    public function testIfSwaggerFileReturnsErrorOnInvalidFile()
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

    public function testIfSwaggerFileReturnsErrorOnFileNotFound()
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
}
