<?php

namespace Cloudflare\Api;

use GuzzleHttp\TransferStats;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * API Connection Interface
 * - Example use: https://gist.github.com/prolificjones82/046bff3cb466ec69894c93a569a521cb
 */
abstract class ConnectionInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $responseFormats = ['json', 'xml'];

    /**
     * Init class
     *
     * @var string $url
     * @var string $format
     * @var array  $headers
     */
    public function __construct(string $url, string $format, array $headers = [])
    {
        $this->setBaseUrl($url);
        $this->setFormat($format);

        if (!isset($headers['Cache-Control'])) {
            $headers['Cache-Control'] = 'no-cache';
        }

        if (!isset($headers['Content-Type']) && count($this->getResponseFormats()) > 0 && !empty($this->getFormat())) {
            if (in_array($this->getFormat(), $this->getResponseFormats())) {
                $headers['Content-Type'] = sprintf('application/%s', $this->getFormat());
            }
        }

        $this->setHeaders($headers);
    }

    /**
     * Make API Request
     *
     * @var string $method - GET/POST/PUT/DELETE
     * @var string $apiUrl
     * @var array  $data
     * @var array  $headers
     *
     * @return JsonResponse
     */
    protected function makeRequest(string $method, string $apiUrl, array $data = [], array $headers = []): JsonResponse
    {
        $this->setHeaders($headers);
        $client = new HttpClient();

        $apiUrl = sprintf('%s%s', rtrim($this->getBaseUrl(), '/'), str_replace($this->getBaseUrl(), '', $apiUrl));
        $response = [
            'method' => $method,
            'url' => $apiUrl,
            'data' => $data,
            'headers' => $this->headers,
        ];

        try {
            $options = [
                'debug' => false,
                'headers' => $this->getHeaders(),
                'idn_conversion' => false,
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                },
            ];

            if (!empty($data)) {
                $options['body'] = json_encode($data);
            }

            $res = $client->request($method, $apiUrl, $options);
            $response = array_merge($response, [
                'success' => true,
                'code' => $res->getStatusCode(),
                'body' => json_decode($res->getBody()),
            ]);
        } catch (RequestException $e) {
            $response = array_merge($response, [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        } catch (ClientException $ce) {
            $response = array_merge($response, [
                'success' => false,
                'code' => $ce->getCode(),
                'message' => $ce->getMessage(),
            ]);
        } catch (ServerException $se) {
            $response = array_merge($response, [
                'success' => false,
                'code' => $se->getCode() ?? 500,
                'message' => $se->getMessage(),
            ]);
        }

        return new JsonResponse($response, $response['code']);

        // $resp = new ApiResponse();
        // $resp->setUrl($url->__toString());
        // $resp->setStatusCode($res->getStatusCode());
        // $resp->setBody($res->getBody());
        // return $resp;
    }

    /**
     * Get the value of baseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the value of baseUrl
     *
     * @param string $baseUrl
     *
     * @return self
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get the value of format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the value of format
     *
     * @param string $format
     *
     * @return self
     */
    public function setFormat(string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the value of headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the value of headers
     *
     * @param array $headers
     *
     * @return self
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Get the value of responseFormats
     *
     * @return array
     */
    public function getResponseFormats()
    {
        return $this->responseFormats;
    }

    /**
     * Set the value of responseFormats
     *
     * @param array $responseFormats
     *
     * @return self
     */
    public function setResponseFormats(array $responseFormats = [])
    {
        $this->responseFormats = array_merge($this->responseFormats, $responseFormats);

        return $this;
    }
}
