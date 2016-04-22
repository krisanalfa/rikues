<?php

namespace Rikues;

use Rikues\Exceptions\ClientException;
use Rikues\Exceptions\ServerException;

class Rikues
{
    protected $client = null;

    protected $uri = '';

    protected $method = '';

    protected $params = [];

    protected $headers = [];

    protected $options = [];

    public function __construct($uri = '', $method = 'GET')
    {
        $this->uri = $uri;
        $this->method = trim(mb_strtoupper($method));
        $this->client = curl_init($uri);

        $this->bootstrapCurl();
    }

    public function withParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function withHeader($name, $value)
    {
        $name = trim(
            ucfirst(
                mb_strtolower($name)
            )
        );

        $this->headers[] = "$name: $value";
    }

    public function withMethod($method)
    {
        $this->method = trim(mb_strtoupper($method));

        $this->withOption(CURLOPT_CUSTOMREQUEST, $this->method);
    }

    public function withOption($option, $value)
    {
        $this->options[$option] = $value;

        curl_setopt($this->client, $option, $value);
    }

    public function send()
    {
        $this->prepareUriBeforeSend();

        $this->withOption(CURLOPT_HTTPHEADER, $this->headers);

        $response = $this->executeClient();

        $this->closeClient();

        return $response;
    }

    protected function isGetRequest()
    {
        return trim(mb_strtoupper($this->method)) === 'GET';
    }

    protected function prepareUriBeforeSend()
    {
        if ($this->isGetRequest()) {
            $this->withOption(CURLOPT_URL, $this->uri.'?'.http_build_query($this->params));
        } else {
            $this->withOption(CURLOPT_POSTFIELDS, $this->params);
        }
    }

    protected function executeClient()
    {
        return (($response = curl_exec($this->client)) === false)
            ? $this->throwClientException()
            : $this->parseResponse($response);
    }

    protected function parseResponse($response)
    {
        $info = curl_getinfo($this->client);

        if ($this->isErrorResponse($info)) {
            $this->throwServerException($info, $response);
        }

        return $response;
    }

    protected function isErrorResponse($info)
    {
        $statusCode = $info['http_code'];

        return $statusCode > 200 || $statusCode < 200;
    }

    protected function throwServerException($info, $response)
    {
        $statusCode = $info['http_code'];

        throw new ServerException(
            "Server returned an error response with status code $statusCode.",
            $statusCode,
            $response
        );
    }

    protected function throwClientException()
    {
        throw new ClientException($this->getError());
    }

    protected function getError()
    {
        return curl_error($this->client);
    }

    protected function closeClient()
    {
        return curl_close($this->client);
    }

    protected function bootstrapCurl()
    {
        $this->withOption(CURLOPT_CUSTOMREQUEST, $this->method);
        $this->withOption(CURLOPT_RETURNTRANSFER, true);
        $this->withOption(CURLOPT_HEADER, false);
        $this->withOption(CURLINFO_HEADER_OUT, true);
    }

    public function __wakeUp()
    {
        $this->client = curl_init($this->uri);

        $this->bootstrapCurl();
    }
}
