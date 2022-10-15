<?php

namespace Tests;

use GuzzleHttp\Psr7\HttpFactory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class ExampleTest extends TestCase
{
    public function setUp(): void {
        parent::setUp();
//        config(['app.url' => 'http://127.0.0.1/dashboard']);

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_base_endpoint_returns_a_successful_response()
    {

        $response = $this->call('GET', '/pets?foo=bar');

        $psr17Factory = new HttpFactory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($this->app['request']);

        $yamlPath = __DIR__ . '/../petstore.yml';
        $validatorBuilder = (new ValidatorBuilder())
            ->fromYamlFile($yamlPath);
        $requestValidator = $validatorBuilder->getServerRequestValidator();
        $responseValidator = $validatorBuilder->getResponseValidator();

        $op = $requestValidator->validate($psrRequest);

        $psr7Response = $psrHttpFactory->createResponse($response->baseResponse);
        $responseValidator->validate($op, $psr7Response);

        $this->assertEquals(
            '[{"id":123,"name":"foo"}]',
            $this->response->getContent()
        );
    }
}
