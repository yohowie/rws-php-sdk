<?php

namespace RakutenRws\Api;

use RakutenRws\RakutenRwsException;
use RakutenRws\ApiResponse\AppRakutenResponse;

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

    public function execute($parameter)
    {
        $url = $this->genUrl();

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

        $client = $this->client->getHttpClient();
        $method = 'get';
        if (strtoupper($this->getMethod()) !== 'GET') {
            $method = 'post';
        }

        $response = $client->$method($url, $parameter);

        $appresponse = new AppRakutenResponse($this->getOperationName(), $response);

        if ($this->autoSetIterator && $appresponse->isOk()) {
            $data = $appresponse->getData();
            if (!isset($data[$this->arrayName])) {
                throw new RakutenRwsException();
            }

            $items = array();
            foreach ($data[$this->arrayName] as $item) {
                $items[] = $item[$this->entityName];
            }

            $appresponse->setIterator($items);
        }

        return $appresponse;
    }

    public function setVersion($version, $forceVersionCheck = false)
    {
        $versionSignature = preg_replace(
            '/^(\d{4})(\d{2})(\d{2})$/',
            '\\1-\\2-\\3',
            $version
        );
        $this->versionMap[$versionSignature] = $version;
        parent::setVersion($versionSignature, $forceVersionCheck);
    }
}
