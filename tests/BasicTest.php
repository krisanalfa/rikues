<?php

use Rikues\Rikues;

class BasicTest extends PHPUnit_Framework_TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists(Rikues::class));
    }

    public function testObjectCanBeSerialized()
    {
        $rikues = new Rikues("https://httpbin.org/ip");
        $serialized = serialize($rikues);
        $unserialized = unserialize($serialized);

        $response = $unserialized->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(is_array($decoded));
    }

    public function testCanMakeGetRequest()
    {
        $rikues = new Rikues("https://httpbin.org/ip");

        $response = $rikues->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(is_array($decoded));
    }

    public function testCanMakeGetRequestWithParams()
    {
        $rikues = new Rikues("https://httpbin.org/get");

        $rikues->withParam('foo', 'bar');
        $rikues->withParam('baz', 'quux');

        $response = $rikues->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(array_key_exists('foo', $decoded['args']));
        $this->assertTrue(array_key_exists('baz', $decoded['args']));
    }

    public function testCanMakePostRequest()
    {
        $rikues = new Rikues("https://httpbin.org/post");

        $rikues->withParam('foo', 'bar');
        $rikues->withParam('baz', 'quux');

        $rikues->withMethod('POST');

        $response = $rikues->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(array_key_exists('foo', $decoded['form']));
        $this->assertTrue(array_key_exists('baz', $decoded['form']));
    }

    public function testCanMakePatchRequest()
    {
        $rikues = new Rikues("https://httpbin.org/patch");

        $rikues->withParam('foo', 'bar');
        $rikues->withParam('baz', 'quux');

        $rikues->withMethod('PATCH');

        $response = $rikues->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(array_key_exists('foo', $decoded['form']));
        $this->assertTrue(array_key_exists('baz', $decoded['form']));
    }

    public function testCanMakePutRequest()
    {
        $rikues = new Rikues("https://httpbin.org/put");

        $rikues->withParam('foo', 'bar');
        $rikues->withParam('baz', 'quux');

        $rikues->withMethod('PUT');

        $response = $rikues->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(array_key_exists('foo', $decoded['form']));
        $this->assertTrue(array_key_exists('baz', $decoded['form']));
    }

    public function testCanMakeDeleteRequest()
    {
        $rikues = new Rikues("https://httpbin.org/delete");

        $rikues->withParam('foo', 'bar');
        $rikues->withParam('baz', 'quux');

        $rikues->withMethod('DELETE');

        $response = $rikues->send();

        $decoded = json_decode($response, true);

        $this->assertTrue(array_key_exists('foo', $decoded['form']));
        $this->assertTrue(array_key_exists('baz', $decoded['form']));
    }
}
