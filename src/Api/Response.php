<?php

namespace Cloudflare\Api;

use Symfony\Component\HttpFoundation\Response as CoreResponse;

class Response
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $body;

    /**
     * Get the value of url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param string $url
     *
     * @return self
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Retrieves the response code.
     *
     * @return int The response code.
     */
    public function getCode()
    {
        return $this->getStatusCode();
    }

    /**
     * Set the value of statusCode
     *
     * @param int $statusCode
     *
     * @return self
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Get the value of body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the value of body decoded
     *
     * @return object|array
     */
    public function getBodyDecoded($asAssoc = false)
    {
        return json_decode($this->body, $asAssoc);
    }

    /**
     * Set the value of body
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    public function getStatusText(int $code)
    {
        return CoreResponse::$statusTexts[$code];
    }
}
