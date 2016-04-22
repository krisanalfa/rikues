<?php

namespace Rikues;

use Rikues\Exceptions\ClientException;
use Rikues\Exceptions\ServerException;

/**
 * Rikues is a simple cURL library with serialization support.
 *
 * @author Krisan Alfa Timur <krisan47@gmail.com>
 */
class Rikues
{
    /**
     * cURL resource.
     *
     * @var resource
     */
    protected $client = null;

    /**
     * cURL uri.
     *
     * @var string
     */
    protected $uri = '';

    /**
     * cURL HTTP method.
     *
     * @var string
     */
    protected $method = '';

    /**
     * cURL POST body / GET query string.
     *
     * @var array
     */
    protected $params = [];

    /**
     * cURL header.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * cURL option.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Initialize Rikues object.
     *
     * @param string $uri
     * @param string $method
     */
    public function __construct($uri = '', $method = 'GET')
    {
        $this->uri = $uri;
        $this->method = trim(mb_strtoupper($method));
        $this->client = curl_init($uri);

        $this->bootstrapCurl();
    }

    /**
     * Set HTTP parameters for POST. In GET request, it will be generated as a query string.
     *
     * @param string $name
     * @param string $value
     */
    public function withParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * Set header for your cURL request.
     *
     * @param string $name
     * @param string $value
     */
    public function withHeader($name, $value)
    {
        $name = trim(
            ucfirst(
                mb_strtolower($name)
            )
        );

        $this->headers[] = "$name: $value";
    }

    /**
     * Set your cURL HTTP method.
     *
     * @param string $method
     */
    public function withMethod($method)
    {
        $this->method = trim(mb_strtoupper($method));

        $this->withOption(CURLOPT_CUSTOMREQUEST, $this->method);
    }

    /**
     * Chane your cURL option.
     *
     * @param mixed $option
     * @param mixed $value
     *
     * @see https://secure.php.net/manual/en/function.curl-setopt.php
     */
    public function withOption($option, $value)
    {
        $this->options[$option] = $value;

        curl_setopt($this->client, $option, $value);
    }

    /**
     * Send your cURL request.
     *
     * @return string.
     */
    public function send()
    {
        $this->prepareUriBeforeSend();

        $this->withOption(CURLOPT_HTTPHEADER, $this->headers);

        $response = $this->executeClient();

        $this->closeClient();

        return $response;
    }

    /**
     * Determine if this is a GET request.
     *
     * @return bool
     */
    protected function isGetRequest()
    {
        return trim(mb_strtoupper($this->method)) === 'GET';
    }

    /**
     * Change the URI if it's a GET request. We build a query params from `params` attribute.
     *
     * @see https://secure.php.net/manual/en/function.http-build-query.php
     */
    protected function prepareUriBeforeSend()
    {
        if ($this->isGetRequest()) {
            $this->withOption(CURLOPT_URL, $this->uri.'?'.http_build_query($this->params));
        } else {
            $this->withOption(CURLOPT_POSTFIELDS, $this->params);
        }
    }

    /**
     * Execute current cURL resource.
     *
     * @return mixed
     */
    protected function executeClient()
    {
        return (($response = curl_exec($this->client)) === false)
            ? $this->throwClientException()
            : $this->parseResponse($response);
    }

    /**
     * Parse response, whether it's a successful request or not.
     *
     * @param string $response
     *
     * @return mixed
     */
    protected function parseResponse($response)
    {
        $info = curl_getinfo($this->client);

        if ($this->isErrorResponse($info)) {
            $this->throwServerException($info, $response);
        }

        return $response;
    }

    /**
     * Determine if it's an error response.
     *
     * @param  array  $info
     *
     * @return boolean
     */
    protected function isErrorResponse(array $info)
    {
        $statusCode = $info['http_code'];

        return $statusCode > 200 || $statusCode < 200;
    }

    /**
     * Throw server error when the response is not success.
     *
     * @param  array $info
     * @param  string $response
     *
     * @throws \Rikues\Exceptions\ServerException
     */
    protected function throwServerException(array $info, $response)
    {
        $statusCode = $info['http_code'];

        throw new ServerException(
            "Server returned an error response with status code $statusCode.",
            $statusCode,
            $response
        );
    }

    /**
     * Throw client exception when cURL client get error before it gets any response from server.
     *
     * @throws \Rikues\Exceptions\ClientException
     */
    protected function throwClientException()
    {
        throw new ClientException($this->getError());
    }

    /**
     * Ger error from cURL resource
     *
     * @return string
     */
    protected function getError()
    {
        return curl_error($this->client);
    }

    /**
     * Close cURL resource to free up system resource.
     *
     * @return string
     */
    protected function closeClient()
    {
        return curl_close($this->client);
    }

    /**
     * Initialize basic cURL object.
     */
    protected function bootstrapCurl()
    {
        $this->withOption(CURLOPT_CUSTOMREQUEST, $this->method);
        $this->withOption(CURLOPT_RETURNTRANSFER, true);
        $this->withOption(CURLOPT_HEADER, false);
        $this->withOption(CURLINFO_HEADER_OUT, true);
    }

    /**
     * Setting up all option when object being unserialized.
     *
     * @see https://php.net/manual/en/language.oop5.magic.php#object.wakeup
     */
    public function __wakeUp()
    {
        $this->client = curl_init($this->uri);

        $this->bootstrapCurl();

        foreach ($this->options as $option => $value) {
            curl_setopt($this->client, $option, $value);
        }
    }
}
