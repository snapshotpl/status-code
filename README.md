# status-code [![Build Status](https://travis-ci.org/snapshotpl/status-code.svg?branch=master)](https://travis-ci.org/snapshotpl/status-code)
HTTP status code value object implementation in PHP.

## Features
* validation,
* auto setup reason phrase (if known),
* immutable,
* support PSR-7 `Psr\Http\Message\ResponseInterface`.

Supported RFCs:
* https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
* https://tools.ietf.org/html/rfc4918
* https://tools.ietf.org/html/rfc3229
* https://tools.ietf.org/html/rfc5842
* https://tools.ietf.org/html/rfc7538
* https://tools.ietf.org/html/rfc7540
* https://tools.ietf.org/html/rfc7231
* https://tools.ietf.org/html/rfc2518
* https://tools.ietf.org/html/rfc6585
* https://tools.ietf.org/html/rfc2295
* https://tools.ietf.org/html/rfc2774

Supported drafts:
* https://tools.ietf.org/html/draft-ietf-httpbis-legally-restricted-status-04

## Installation

Add to composer:

```json
{
    "require": {
        "snapshotpl/status-code": "^1.0"
    }
}
```

## Usage

```php
$statusCode = new StatusCode(404);

$statusCode->isClientError(); // true
$statusCode->isRfc2516() // true
$statusCode->isServerError(); // false

echo $statusCode; // 404 Not Found
```

You can use it with anny PSR-7 implementation:

```php
$response = new Zend\Diactoros\Response();
$statusCode = StatusCode::createFromResponse($response);
echo $statusCode; // 200 OK
```

```php
$response = new Zend\Diactoros\Response();
$statusCode = new StatusCode(404, 'Not exists');
$newResponse = $statusCode->attachToResponse($response);
echo $newResponse->getStatusCode(); // 404
echo $newResponse->getReasonPhrase(); // Not exists
```