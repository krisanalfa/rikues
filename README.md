# Rikues

Rikues is a simple cURL library with serialization support.

## Basic Usage

```php
use Rikues\Rikues;

$rikues = new Rikues("https://httpbin.org/get");

$response = $rikues->send();
```

## POST Request

```php
use Rikues\Rikues;

$rikues = new Rikues("https://httpbin.org/post");

$rikues->withParam('foo', 'bar');
$rikues->withParam('baz', 'quux');

$rikues->withMethod('POST');

$response = $rikues->send();
```

## Working With Headers

```php
use Rikues\Rikues;

$rikues = new Rikues("https://httpbin.org/get");

$rikues->withHeader('Accept', 'application/json');
$rikues->withHeader('Authorization', 'Bearer xxx');

$response = $rikues->send();
```

## Working With Query Params

```php
use Rikues\Rikues;

$rikues = new Rikues("https://httpbin.org/get");

$rikues->withParam('foo', 'bar');
$rikues->withParam('baz', 'quux');

// Request to https://httpbin.org/get?foo=bar&baz=quux

$response = $rikues->send();
```

## Working With Exception

```php
use Rikues\Rikues;
use Rikues\Exceptions\ClientException;
use Rikues\Exceptions\ServerException;

$rikues = new Rikues("https://httpbin.org/get");

try {
    $response = $rikues->send();
} catch (ClientException $e) {
    echo $e->getMessage().PHP_EOL;
} catch (ServerException $e) {
    $serverResponse = $e->response;
}
```
