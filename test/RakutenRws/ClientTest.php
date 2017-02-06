<?php

namespace RakutenRws;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

use \Mockery;

use GuzzleHttp\Client as GClient;

class ClientTest extends TestCase
{
    /**
     *
     * @test
     */
    public function testGetAuthorizeUrl()
    {
        $clinet = new Client();

        $clinet->setApplicationId('123');
        $clinet->setSecret('foo-bar');
        $clinet->setRedirectUrl('http://example.com');
        $url = $clinet->getAuthorizeUrl('the_scope');

        $this->assertEquals('https://app.rakuten.co.jp/services/authorize?response_type=code&client_id=123&redirect_uri=http%3A%2F%2Fexample.com&scope=the_scope', $url);
    }

    /**
     *
     * @test
     */
    public function testfetchAccessTokenFromCode1()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('post')
            ->with(
                'https://app.rakuten.co.jp/services/token',
                [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => '123',
                    'client_secret' => 'foo-bar',
                    'code'          => 'codecode',
                    'redirect_uri'  => 'http://example.com'
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'access_token'  => 'abc',
                'refresh_token' => 'def',
                'token_type'    => 'BEARER',
                'expires_in'    => 300,
                'scope'         => 'the_scope'
            ])));

        $client = new Client($httpClient);

        $client->setApplicationId('123');
        $client->setSecret('foo-bar');
        $client->setRedirectUrl('http://example.com');

        $this->assertEquals('abc', $client->fetchAccessTokenFromCode('codecode'));
        $this->assertEquals('abc', $client->getAccessToken());
    }

    /**
     *
     * @test
     */
    public function testfetchAccessTokenFromCode2()
    {

        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('post')
            ->with(
                "https://app.rakuten.co.jp/services/token",
                [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => '123',
                    'client_secret' => 'foo-bar',
                    'code'          => 'codecode',
                    'redirect_uri'  => 'http://example.com'
                ]
            )
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'error'             => 'invalid_request',
                'error_description' => 'invalid code'
            ])));

        $client = new Client($httpClient);

        $client->setApplicationId('123');
        $client->setSecret('foo-bar');
        $client->setRedirectUrl('http://example.com');

        $this->assertNull($client->fetchAccessTokenFromCode('codecode'));
    }

    /**
     *
     * @Test
     */
    public function testfetchAccessTokenFromCode3()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('post')
            ->with(
                "https://app.rakuten.co.jp/services/token",
                [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => '123',
                    'client_secret' => 'foo-bar',
                    'code'          => 'codecode',
                    'redirect_uri'  => 'http://example.com'
                ])
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'access_token'  => 'abc',
                'refresh_token' => 'def',
                'token_type'    => 'BEARER',
                'expires_in'    => 300,
                'scope'         => 'the_scope'
            ])));


        $clinet = new Client($httpClient);

        $clinet->setApplicationId('123');
        $clinet->setSecret('foo-bar');
        $clinet->setRedirectUrl('http://example.com');

        $_GET['code'] = 'codecode';
        $this->assertEquals('abc', $clinet->fetchAccessTokenFromCode());
        $this->assertEquals('abc', $clinet->getAccessToken());
    }

    /**
     *
     * @test
     * @expectedException LogicException
     */
    public function testfetchAccessTokenFromCode4_Error()
    {
        $clinet = new Client();
        unset($_GET['code']);
        $clinet->fetchAccessTokenFromCode();
    }

    /**
     *
     * @test
     */
    public function testfetchAccessTokenFromCode5_BrokenData()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('post')
            ->with(
                "https://app.rakuten.co.jp/services/token",
                [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => '123',
                    'client_secret' => 'foo-bar',
                    'code'          => 'codecode',
                    'redirect_uri'  => 'http://example.com'
                ])
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'error'             => 'invalid_request',
                'error_description' => 'invalid code'
            ])));

        $clinet = new Client($httpClient);

        $clinet->setApplicationId('123');
        $clinet->setSecret('foo-bar');
        $clinet->setRedirectUrl('http://example.com');

        $this->assertNull($clinet->fetchAccessTokenFromCode('codecode'));
    }

    /**
     * testExecute
     *
     * @test
     */
    public function testExecute()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108',
                [
                    'http_errors'=>false,
                    'query' => [
                        'applicationId' => '123',
                        'affiliateId'   => '456'
                    ]
                ])
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'data' => 'the response'
            ])));

        $clinet = new Client($httpClient);
        $clinet->setApplicationId('123');
        $clinet->setAffiliateId('456');
        $response = $clinet->execute('DummyAppRakutenApi2');
        $this->assertInstanceOf(ApiResponse::class, $response);
    }

    /**
     * testExecute
     *
     * @test
     */
    public function testExecuteWithOperationAlias()
    {
        $httpClient = Mockery::mock(GClient::class);
        $httpClient->shouldReceive('request')
            ->with(
                'GET',
                'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108',
                [
                    'http_errors'=>false,
                    'query' => [
                        'applicationId' => '123',
                        'affiliateId'   => '456'
                    ]
                ])
            ->once()
            ->andReturn(new Response(200, [], json_encode([
                'data' => 'the response'
            ])));

        $clinet = new Client($httpClient);
        $clinet->setApplicationId('123');
        $clinet->setAffiliateId('456');
        $response = $clinet->execute('DummyAppRakuten/Api2');
        $this->assertInstanceOf(ApiResponse::class, $response);
    }

    /**
     *
     * @test
     * @expectedException LogicException
     */
    public function testExecute_with_WrongOperation()
    {
        $clinet = new Client();

        $clinet->execute('WrongOperation');
    }
}
