<?php

namespace RakutenRws;

/**
 * This file is part of Rakuten Web Service SDK
 *
 * (c) Rakuten, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with source code.
 */

/**
 * Class HttpResponse
 * @package RakutenRws
 */
class HttpResponse
{
    protected
        $requestUrl = null,
        $parameter  = null,
        $code       = 200,
        $contents   = null,
        $headers    = null;

    /**
     * HttpResponse constructor.
     * @param $requestUrl
     * @param $parameter
     * @param $code
     * @param $headers
     * @param $contents
     */
    public function __construct($requestUrl, $parameter, $code, $headers, $contents)
    {
        $this->requestUrl = $requestUrl;
        $this->parameter  = $parameter;
        $this->code       = $code;
        $this->headers    = $headers;
        $this->contents   = $contents;
    }

    public function getUrl() {
        return $this->requestUrl;
    }

    public function getParameter() {
        return $this->parameter;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
