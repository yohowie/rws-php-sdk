<?php

namespace RakutenRws\Api;

use RakutenRws\RakutenRwsException;
use RakutenRws\ApiResponse\AppRakutenResponse;
use RakutenRws\HttpResponse;

use GuzzleHttp\Promise\Promise;

/**
 * This file is part of Rakuten Web Service SDK
 *
 * (c) Rakuten, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with source code.
 */

/**
 * API for app.rakuten.co.jp
 *
 * @package RakutenRws
 * @subpackage Api
 */
abstract class AppRakutenApi extends Base
{
    protected
        $baseUrl = "",
        $isRequiredAccessToken = true,
        $arrayName = "Items",
        $entityName = "Item";


    abstract public function getService();
    abstract public function getOperation();

    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    protected function genUrl()
    {
        $url  = $this->baseUrl;
        $url .= '/'.$this->getService();
        $url .= '/'.$this->getOperation();
        $url .= '/'.$this->versionMap[$this->version];
        return $url;
    }

    public function getMethod()
    {
        return 'GET';
    }

    protected function buildParameters($parameter)
    {
        if ($this->isRequiredAccessToken) {
            $parameter['access_token'] = $this->client->getAccessToken();
        } else {
            $parameter['applicationId'] = $this->client->getApplicationId();
        }

        if ($this->client->getAffiliateId()) {
            $parameter['affiliateId'] = $this->client->getAffiliateId();
        }

        unset($parameter['callback']);
        unset($parameter['format']);

        return $parameter;
    }

    /**
     * @param $url
     * @param $parameter
     * @param $response
     * @return AppRakutenResponse
     */
    protected function buildResponse($url, $parameter, $response)
    {
        $httpResponse = new HttpResponse(
            $url,
            $parameter,
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody()->getContents()
        );

        $appresponse = new AppRakutenResponse($this->getOperationName(), $httpResponse);

        if ($this->autoSetIterator && $appresponse->isOk()) {
            $data = $appresponse->getData();
            $items = array();
            if (isset($data[$this->arrayName])) {
                foreach ($data[$this->arrayName] as $item) {
                    $items[] = $item[$this->entityName];
                }
            }
            $appresponse->setIterator($items);
        }

        return $appresponse;
    }

    /**
     * @param $parameter
     * @return AppRakutenResponse
     */
    public function execute($parameter)
    {
        $url = $this->genUrl();
        $method = 'GET';
        if (strtoupper($this->getMethod()) !== 'GET') {
            $method = 'POST';
        }

        $options = ['http_errors' => false];
        $options[$method == 'POST' ? 'form_params' : 'query'] = $this->buildParameters($parameter);
        $client = $this->client->getHttpClient();
        $response = $client->request($method, $url, $options);
        return $this->buildResponse($url, $parameter, $response);
    }

    /**
     * @param $parameter
     * @return Promise
     */
    public function executeAsync($parameter)
    {
        $promise = new Promise(function () use (&$promise, $parameter) {

            $url = $this->genUrl();
            $method = 'GET';
            if (strtoupper($this->getMethod()) !== 'GET') {
                $method = 'POST';
            }

            $options = ['http_errors' => false];
            $options[$method == 'POST' ? 'form_params' : 'query'] = $this->buildParameters($parameter);
            $client = $this->client->getHttpClient();
            $response = $client->requestAsync($method, $url, $options)->wait();

            $promise->resolve($this->buildResponse($url, $parameter, $response));

        });
        return $promise;
    }

    /**
     * @param $version
     * @param bool $forceVersionCheck
     */
    public function setVersion($version, $forceVersionCheck = false)
    {
        $versionSignature = preg_replace(
            '/^(\d{4})(\d{2})(\d{2})$/',
            '\\1-\\2-\\3',
            $version
        );
        if ( $forceVersionCheck) {
            $this->versionMap[$versionSignature] = $version;
        }
        parent::setVersion($versionSignature, $forceVersionCheck);
    }
}
