<?php

namespace RakutenRws\Api;

use PHPUnit\Framework\TestCase;
use RakutenRws\Api\Definition\DummyAppRakutenApi1;
use RakutenRws\Api\Definition\DummyAppRakutenApi2;
use RakutenRws\Api\Definition\DummyAppRakutenApi3;
use RakutenRws\HttpResponse;
use RakutenRws\Client;

use GuzzleHttp\Psr7\Response;

use \Mockery;

use GuzzleHttp\Client as GClient;




class AppRakutenApiTest extends TestCase
{
    /**
     *
     * @test
     */
    public function testExecuteAppRakutenApi()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation1/19890108',
                [
                    'http_errors' => false,
                    'query' => [
                        'access_token' => 'abc',
                        'affiliateId' => '456'
                    ]
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'Items' => array(array('Item' => 'data'))
            ])));

        $rwsClient = Mockery::mock(Client::class);
        $rwsClient
            ->shouldReceive('getHttpClient')
            ->andReturn($httpClient)
            ->shouldReceive('getAccessToken')
            ->andReturn('abc')
            ->shouldReceive('getApplicationId')
            ->andReturn('123')
            ->shouldReceive('getAffiliateId')
            ->andReturn('456');

        $api = new DummyAppRakutenApi1($rwsClient);
        $response = $api->execute(array());

        $this->assertEquals('DummyService', $api->getService());
        $this->assertEquals('DummyOperation1', $api->getOperation());
        $this->assertEquals('RakutenRws\Api\Definition\DummyAppRakutenApi1', $api->getOperationName());
        $this->assertEquals('1989-01-08', $api->getVersion());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals(array(array('Item' => 'data')), $response['Items']);
    }

    /**
     *
     * @test
     */
    public function testExecuteNonAuthorizedAppRakutenApi()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108',
                [
                    'http_errors' => false,
                    'query' => [
                        'applicationId' => '123',
                        'affiliateId'   => '456'
                    ]
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'data' => 'the response'
            ])));

        $rwsClient = Mockery::mock(Client::class);
        $rwsClient
            ->shouldReceive('getHttpClient')
            ->andReturn($httpClient)
            ->shouldReceive('getAccessToken')
            ->andReturn('abc')
            ->shouldReceive('getApplicationId')
            ->andReturn('123')
            ->shouldReceive('getAffiliateId')
            ->andReturn('456');

        $api = new DummyAppRakutenApi2($rwsClient);
        $response = $api->execute(array());

        $this->assertEquals('DummyService', $api->getService());
        $this->assertEquals('DummyOperation2', $api->getOperation());
        $this->assertEquals(DummyAppRakutenApi2::class, $api->getOperationName());
        $this->assertEquals('1989-01-08', $api->getVersion());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('the response', $response['data']);
    }

    /**
     *
     * @test
     */
    public function testExecutePostAppRakutenApi()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'POST',
                "https://app.rakuten.co.jp/services/api/DummyService/DummyOperation3/19890108",
                [
                    'http_errors' => false,
                    'form_params' => [
                        'access_token' => 'abc',
                        'affiliateId'   => '456'
                    ]
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'data' => 'the response'
            ])));

        $rwsClient = Mockery::mock(Client::class);
        $rwsClient
            ->shouldReceive('getHttpClient')
            ->andReturn($httpClient)
            ->shouldReceive('getAccessToken')
            ->andReturn('abc')
            ->shouldReceive('getApplicationId')
            ->andReturn('123')
            ->shouldReceive('getAffiliateId')
            ->andReturn('456');

        $api = new DummyAppRakutenApi3($rwsClient);
        $response = $api->execute(array());

        $this->assertEquals('DummyService', $api->getService());
        $this->assertEquals('DummyOperation3', $api->getOperation());
        $this->assertEquals(DummyAppRakutenApi3::class, $api->getOperationName());
        $this->assertEquals('1989-01-08', $api->getVersion());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('the response', $response['data']);
    }

    /**
     *
     * @test
     */
    public function testSetVersion()
    {
        $client = new Client();
        $api = new DummyAppRakutenApi1($client);
        $api->setVersion('2012-01-08');
        $this->assertEquals('2012-01-08', $api->getVersion());
    }

    /**
     *
     * @test
     */
    public function testSetVersionWithoutHyphen()
    {
        $client = new Client();
        $api = new DummyAppRakutenApi1($client);
        $api->setVersion('20120108');
        $this->assertEquals('2012-01-08', $api->getVersion());
    }

    /**
     *
     * @test
     */
    public function testSetVersionWithNumber()
    {
        $client = new Client();
        $api = new DummyAppRakutenApi1($client);
        $api->setVersion(20120108);
        $this->assertEquals('2012-01-08', $api->getVersion());
    }

    /**
     *
     * @test
     * @expectedException \RakutenRws\RakutenRwsException
     */
    public function testSetVersion_When_Sets_Wrong_Version()
    {
        $client = new Client();
        $api = new DummyAppRakutenApi1($client);
        $api->setVersion('2020-01-08');
    }

    /**
     *
     * @test
     */
    public function testExecuteNonAuthorizedAppRakutenApi_With_callback()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108',
                [
                    'http_errors' => false,
                    'query' => [
                        'applicationId' => '123',
                        'affiliateId'   => '456'
                    ]
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'data' => 'the response'
            ])));

        $rwsClient = Mockery::mock(Client::class);
        $rwsClient
            ->shouldReceive('getHttpClient')
            ->andReturn($httpClient)
            ->shouldReceive('getAccessToken')
            ->andReturn('abc')
            ->shouldReceive('getApplicationId')
            ->andReturn('123')
            ->shouldReceive('getAffiliateId')
            ->andReturn('456');

        $api = new DummyAppRakutenApi2($rwsClient);
        $response = $api->execute(array('callback' => 'it_will_be_deleted'));

        $this->assertEquals('DummyService', $api->getService());
        $this->assertEquals('DummyOperation2', $api->getOperation());
        $this->assertEquals(DummyAppRakutenApi2::class, $api->getOperationName());
        $this->assertEquals('1989-01-08', $api->getVersion());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('the response', $response['data']);
    }

    /**
     *
     * @test
     */
    public function testExecuteNonAuthorizedAppRakutenApi_With_format()
    {

        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                "https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108",
                [
                    'http_errors' => false,
                    'query' => [
                        'applicationId' => '123',
                        'affiliateId'   => '456'
                    ]
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'data' => 'the response'
            ])));

        $rwsClient = Mockery::mock(Client::class);
        $rwsClient
            ->shouldReceive('getHttpClient')
            ->andReturn($httpClient)
            ->shouldReceive('getAccessToken')
            ->andReturn('abc')
            ->shouldReceive('getApplicationId')
            ->andReturn('123')
            ->shouldReceive('getAffiliateId')
            ->andReturn('456');

        $api = new DummyAppRakutenApi2($rwsClient);
        $response = $api->execute(array('format' => 'it_will_be_deleted'));

        $this->assertEquals('DummyService', $api->getService());
        $this->assertEquals('DummyOperation2', $api->getOperation());
        $this->assertEquals(DummyAppRakutenApi2::class, $api->getOperationName());
        $this->assertEquals('1989-01-08', $api->getVersion());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('the response', $response['data']);
    }

    /**
     *
     * @test
     */
    public function testExecuteAppRakutenApi_with_BrokenData()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                "https://app.rakuten.co.jp/services/api/DummyService/DummyOperation1/19890108",
                [
                    'http_errors' => false,
                    'query' => [
                        'access_token' => 'abc',
                        'affiliateId'   => '456'
                    ]
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'Ooooooohhhhhhhh!!!!'
            ])));

        $rwsClient = Mockery::mock(Client::class);
        $rwsClient
            ->shouldReceive('getHttpClient')
            ->andReturn($httpClient)
            ->shouldReceive('getAccessToken')
            ->andReturn('abc')
            ->shouldReceive('getApplicationId')
            ->andReturn('123')
            ->shouldReceive('getAffiliateId')
            ->andReturn('456');

        $api = new DummyAppRakutenApi1($rwsClient);
        $api->execute(array());
    }
}
