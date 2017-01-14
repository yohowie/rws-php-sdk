<?php

namespace RakutenRws\Api;

use PHPUnit\Framework\TestCase;
use RakutenRws\HttpResponse;
use RakutenRws\Client;
use RakutenRws\HttpClient\AbstractHttpClient;
use RakutenRws\Api\Definition\DummyAppRakutenApi1;
use RakutenRws\Api\Definition\DummyAppRakutenApi2;
use RakutenRws\Api\Definition\DummyAppRakutenApi3;
use RakutenRws\RakutenRwsException;



class AppRakutenApiTest extends TestCase
{
    /**
     *
     * @test
     */
    public function testExecuteAppRakutenApi()
    {
        $httpClient = $this->getMockBuilder(AbstractHttpClient::class)
            ->setMethods(['post', 'get'])
            ->setMockClassName('httpClient_for_'.__FUNCTION__)
            ->getMock();

        $url = 'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation1/19890108';
        $param = array(
            'access_token' => 'abc'
        );

        $httpResponse = new HttpResponse($url, $param, 200, array(), json_encode(array(
            'Items' => array(array('Item' => 'data'))
        )));

        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($url),
                $this->equalTo($param)
            )
            ->will($this->returnValue($httpResponse));

        $rwsClient = $this->getMockBuilder(Client::class)
            ->setMethods(['getAccessToken', 'getHttpClient'])
            ->setMockClassName('rwsClient_for_'.__FUNCTION__)
            ->getMock();

        $rwsClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $rwsClient->expects($this->once())
            ->method('getAccessToken')
            ->will($this->returnValue('abc'));

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
        $httpClient = $this->getMockBuilder(AbstractHttpClient::class)
            ->setMethods(['post', 'get'])
            ->setMockClassName('httpClient_for_'.__FUNCTION__)
            ->getMock();

        $url = 'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108';
        $param = array(
            'applicationId' => '123',
            'affiliateId'   => '456'
        );

        $httpResponse = new HttpResponse($url, $param, 200, array(), json_encode(array(
            'data' => 'the response'
        )));

        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($url),
                $this->equalTo($param)
            )
            ->will($this->returnValue($httpResponse));

        $rwsClient = $this->getMockBuilder(Client::class)
            ->setMethods(['getHttpClient', 'getApplicationId', 'getAffiliateId'])
            ->setMockClassName('rwsClient_for_'.__FUNCTION__)
            ->getMock();

        $rwsClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $rwsClient->expects($this->once())
            ->method('getApplicationId')
            ->will($this->returnValue('123'));

        $rwsClient->expects($this->any())
            ->method('getAffiliateId')
            ->will($this->returnValue('456'));

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
        $httpClient = $this->getMockBuilder(AbstractHttpClient::class)
            ->setMethods(['post', 'get'])
            ->setMockClassName('httpClient_for_'.__FUNCTION__)
            ->getMock();

        $url = 'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation3/19890108';
        $param = array(
            'access_token' => 'abc'
        );

        $httpResponse = new HttpResponse($url, $param, 200, array(), json_encode(array(
            'data' => 'the response'
        )));

        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo($url),
                $this->equalTo($param)
            )
            ->will($this->returnValue($httpResponse));


        $rwsClient = $this->getMockBuilder(Client::class)
            ->setMethods(['getHttpClient', 'getAccessToken'])
            ->setMockClassName('rwsClient_for_'.__FUNCTION__)
            ->getMock();

        $rwsClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $rwsClient->expects($this->once())
            ->method('getAccessToken')
            ->will($this->returnValue('abc'));

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
     * @expectedException RakutenRws\RakutenRwsException
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
        $httpClient = $this->getMockBuilder(AbstractHttpClient::class)
            ->setMethods(['post', 'get'])
            ->setMockClassName('httpClient_for_'.__FUNCTION__)
            ->getMock();

        $url = 'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108';
        $param = array(
            'applicationId' => '123',
            'affiliateId'   => '456'
        );

        $httpResponse = new HttpResponse($url, $param, 200, array(), json_encode(array(
            'data' => 'the response'
        )));

        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($url),
                $this->equalTo($param)
            )
            ->will($this->returnValue($httpResponse));

        $rwsClient = $this->getMockBuilder(Client::class)
            ->setMethods(['getHttpClient', 'getAccessToken', 'getAffiliateId', 'getApplicationId'])
            ->setMockClassName('rwsClient_for_'.__FUNCTION__)
            ->getMock();

        $rwsClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $rwsClient->expects($this->once())
            ->method('getApplicationId')
            ->will($this->returnValue('123'));

        $rwsClient->expects($this->any())
            ->method('getAffiliateId')
            ->will($this->returnValue('456'));

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
        $httpClient = $this->getMockBuilder(AbstractHttpClient::class)
            ->setMethods(['post', 'get'])
            ->setMockClassName('httpClient_for_'.__FUNCTION__)
            ->getMock();

        $url = 'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation2/19890108';
        $param = array(
            'applicationId' => '123',
            'affiliateId'   => '456'
        );

        $httpResponse = new HttpResponse($url, $param, 200, array(), json_encode(array(
            'data' => 'the response'
        )));

        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($url),
                $this->equalTo($param)
            )
            ->will($this->returnValue($httpResponse));

        $rwsClient = $this->getMockBuilder(Client::class)
            ->setMethods(['getHttpClient', 'getAccessToken', 'getAffiliateId', 'getApplicationId'])
            ->setMockClassName('rwsClient_for_'.__FUNCTION__)
            ->getMock();

        $rwsClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $rwsClient->expects($this->once())
            ->method('getApplicationId')
            ->will($this->returnValue('123'));

        $rwsClient->expects($this->any())
            ->method('getAffiliateId')
            ->will($this->returnValue('456'));

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
     * @expectedException RakutenRws\RakutenRwsException
     */
    public function testExecuteAppRakutenApi_with_BrokenData()
    {
        $httpClient = $this->getMockBuilder(AbstractHttpClient::class)
            ->setMethods(['post', 'get'])
            ->setMockClassName('httpClient_for_'.__FUNCTION__)
            ->getMock();

        $url = 'https://app.rakuten.co.jp/services/api/DummyService/DummyOperation1/19890108';
        $param = array(
            'access_token' => 'abc'
        );

        $httpResponse = new HttpResponse($url, $param, 200, array(), json_encode(array(
            'Ooooooohhhhhhhh!!!!'
        )));

        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($url),
                $this->equalTo($param)
            )
            ->will($this->returnValue($httpResponse));

        $rwsClient = $this->getMockBuilder(Client::class)
            ->setMethods(['getHttpClient', 'getAccessToken'])
            ->setMockClassName('rwsClient_for_'.__FUNCTION__)
            ->getMock();

        $rwsClient->expects($this->once())
            ->method('getHttpClient')
            ->will($this->returnValue($httpClient));

        $rwsClient->expects($this->once())
            ->method('getAccessToken')
            ->will($this->returnValue('abc'));

        $api = new DummyAppRakutenApi1($rwsClient);
        $api->execute(array());
    }
}
