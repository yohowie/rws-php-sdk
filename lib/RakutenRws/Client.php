<?php

namespace RakutenRws;

use GuzzleHttp\Client as GClient;

use RakutenRws\RakutenRwsException;

/**
 * This file is part of Rakuten Web Service SDK
 *
 * (c) Rakuten, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with source code.
 */

/**
 * Class Client
 * @package RakutenRws
 */
class Client
{
    const VERSION = '2.0.0-dev';

    const DEFAULT_BASE_URL = 'https://app.rakuten.co.jp/services/api';

    protected
        $baseUrl         = null,
        $developerId     = null,
        $secret          = null,
        $accessToken     = null,
        $accessTokenInfo = null,
        $redirectUrl     = null,
        $httpClient      = null,
        $affiliateId     = null,
        $options         = array();

    /**
     * Client constructor.
     * @param array $options
     * @param $baseUrl API Baseurl if you change.
     *
     *      * option parameter
     *   - keys
     */
    public function __construct(GClient $client = null, $options = array())
    {
        if (!extension_loaded('openssl')) {
            // @codeCoverageIgnoreStart
            throw new RakutenRwsException('openssl extension is not loaded.');
            // @codeCoverageIgnoreEnd
        }

        $this->httpClient = $client ? $client : new GClient();
        $this->options = $options;
    }


    /**
     * Sets the DeveloperID
     *
     * @param string $developerId The DeveloperID
     * @return $this
     */
    public function setApplicationId($developerId)
    {
        $this->developerId = $developerId;
        return $this;
    }

    /**
     * Gets DeveloperID
     *
     * @return string|null The DeveloperID
     */
    public function getApplicationId()
    {
        return $this->developerId;
    }

    /**
     * Sets the AffiliateID
     *
     * @param string $affiliateId The AffiliateID
     * @return $this
     */
    public function setAffiliateId($affiliateId)
    {
        $this->affiliateId = $affiliateId;
        return $this;
    }

    /**
     * Gets AffilateID
     *
     * @return string|null The AffiliateID
     */
    public function getAffiliateId()
    {
        return $this->affiliateId;
    }

    /**
     * Sets Application Secret
     *
     * @param string $secret The Application Secret
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * Sets Redirect Url
     *
     * @param string $redirectUrl The Redirect URL
     * @return $this
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Gets OAuth2 Authorize URL
     *
     * @param string $scope The scopes that is separated by ','
     * @return string The Authorize URL
     */
    public function getAuthorizeUrl($scope)
    {
        $url = 'https://app.rakuten.co.jp/services/authorize';
        $parameter = [
            'response_type' => 'code',
            'client_id'     => $this->developerId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $scope
        ];

        return $url.'?'.http_build_query($parameter);
    }

    /**
     * Gets OAuth2 Access Token URL
     *
     * @return string The OAuth2 Access Token URL
     */
    public function getAccessTokenUrl()
    {
        return 'https://app.rakuten.co.jp/services/token';
    }

    /**
     * Fetches OAuth2 AccessToken from Code
     *
     * @param string $code The Code
     * @return string The Access Token, If response is invalid return null
     * @throws LogicException
     */
    public function fetchAccessTokenFromCode($code)
    {
        $url = $this->getAccessTokenUrl();
        $parameter = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->developerId,
            'client_secret' => $this->secret,
            'code'          => $code,
            'redirect_uri'  => $this->redirectUrl
        ];

        $response = $this->httpClient->post(
            $url,
            $parameter
        );

        if ($response->getStatusCode() == 200) {
            $this->accessTokenInfo = json_decode($response->getBody()->getContents(), true);
            if (isset($this->accessTokenInfo['access_token'])) {
                $this->accessToken = $this->accessTokenInfo['access_token'];

                return $this->accessToken;
            }
        }

        return null;
    }

    /**
     * Set AccessToken
     *
     * @param $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }


    /**
     * Gets the fetched AccessToken
     *
     * @return string|null The AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return \GuzzleHttp\Client|null
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Sets Application Secret
     *
     * @param string $secret The Application Secret
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * loadClass
     *
     * @param $operation
     * @param $version
     * @param $forceVersionCheck
     * @return mixed
     */
    protected function loadClass($operation, $version, $forceVersionCheck)
    {
        $operation = preg_replace('/\//', '', $operation);
        $className = 'RakutenRws\Api\Definition\\'.$operation;
        if (!class_exists($className)) {
            throw new \LogicException('Operation is not definied.');
        }
        $api = new $className($this, $this->options);
        $api->setBaseUrl($this->baseUrl ? $this->baseUrl : self::DEFAULT_BASE_URL);
        if ($version !== null) {
            $api->setVersion($version, $forceVersionCheck);
        }
        return $api;
    }

    /**
     * Executes API
     *
     * @param string $operation The operation name
     * @param array  $parameter The request parameter
     * @param string $version   The API version
     * @param boolean $forceVersionCheck   The API version
     * @throws \LogicException
     * @throws RakutenRwsException
     * @return mixed
     */
    public function execute($operation, $parameter = array(), $version = null, $forceVersionCheck = false)
    {
        $api = $this->loadClass($operation, $version, $forceVersionCheck);
        return $api->execute($parameter);
    }

    /**
     * Executes Async API
     *
     * @param string $operation The operation name
     * @param array  $parameter The request parameter
     * @param string $version   The API version
     * @param boolean $forceVersionCheck   The API version
     * @throws \LogicException
     * @throws RakutenRwsException
     * @return mixed
     */
    public function executeAsync($operation, $parameter = array(), $version = null, $forceVersionCheck = false)
    {
        $api = $this->loadClass($operation, $version, $forceVersionCheck);
        return $api->executeAsync($parameter);
    }
}
