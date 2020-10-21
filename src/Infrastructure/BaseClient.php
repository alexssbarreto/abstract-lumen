<?php

namespace Abtechi\Laravel\Infrastructure;

use Abtechi\Laravel\Result;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;

/**
 * Class BaseClient
 * @package Abtechi\Infrastructure
 */
abstract class BaseClient
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ServiceRegistryContract
     */
    protected $services;

    /**
     * @var
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $guzzleParams = [
        'headers' => [],
        'timeout' => 40
    ];

    /** @var   */
    protected $presenter;

    /**
     * RestClient constructor.
     * @param Client $client
     * @param Request $request
     */
    public function __construct(Client $client, Request $request)
    {
        $this->client = $client;
        $this->injectHeaders($request);
    }

    /**
     * @param Request $request
     */
    public abstract function injectHeaders(Request $request);

    /**
     * @param Response $response
     * @return mixed
     */
    public abstract function presenterResponse(Response $response, $presenter = null);

    /**
     * @param $url
     * @return mixed
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->guzzleParams['headers'] = $headers;
    }

    /**
     * @param $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->guzzleParams['headers']['Content-Type'] = $contentType;

        return $this;
    }

    /**
     * @param $contentSize
     * @return $this
     */
    public function setContentSize($contentSize)
    {
        $this->guzzleParams['headers']['Content-Length'] = $contentSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->guzzleParams['headers'];
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->guzzleParams['body'] = $body;

        return $this;
    }

    /**
     * @param array $files
     * @return $this
     */
    public function setFiles($files)
    {
        // Get rid of everything else
        $this->setHeaders(array_intersect_key($this->getHeaders(), ['X-User' => null, 'X-Token-Scopes' => null]));

        if (isset($this->guzzleParams['body'])) unset($this->guzzleParams['body']);

        $this->guzzleParams['timeout'] = 20;
        $this->guzzleParams['multipart'] = [];

        foreach ($files as $key => $file) {
            $this->guzzleParams['multipart'][] = [
                'name' => $key,
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
        }

        return $this;
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url)
    {
        return $this->send($url, 'post', $this->guzzleParams);
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($url)
    {
        return $this->send($url, 'put', $this->guzzleParams);
    }

    /**
     * @param $url
     * @return Result
     */
    public function get($url)
    {
        return $this->send($url, 'get', $this->guzzleParams);
    }

    /**
     * @param $url
     * @return Result
     */
    public function delete($url)
    {
        return $this->send($url, 'delete', $this->guzzleParams);
    }

    /**
     * @param $url
     * @param $method
     * @param $parameters
     * @return Result
     */
    private function send($url, $method, $parameters)
    {
        $url = $this->prepareUrl($url);

        try {
            $response = $this->client->{$method}(
                $url,
                $parameters
            );
        } catch (ConnectException $e) {
            throw new UnableToExecuteRequestException();
        } catch (RequestException $e) {
            return new Result(false, $e->getMessage(), $e->getResponse());
        }

        return new Result(true, null, $this->presenterResponse($response));
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    private function prepareUrl($url)
    {
        if (! $this->baseUrl) {
            throw new \Exception('Necessário informar a baseUrl.');
        }

        return $this->baseUrl . $url;
    }
}