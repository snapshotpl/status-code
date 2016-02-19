<?php

namespace Snapshotpl\StatusCode;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * StatusCode
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
final class StatusCode
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;

    /**
     * @var array
     */
    private static $phraseMap = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Permanent Redirect',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * @var array
     */
    private static $rfc2516 = [
        100, 101,
        200, 201, 202, 203, 204, 205, 206,
        300, 301, 302, 303, 304, 305, 307,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417,
        500, 501, 502, 503, 504, 505,
    ];

    /**
     * @param ResponseInterface $response
     *
     * @return self
     */
    public static function createFromResponse(ResponseInterface $response)
    {
        return new self($response->getStatusCode(), $response->getReasonPhrase());
    }

    /**
     * @param int $statusCode
     * @param string $reasonPhrase
     *
     * @throws InvalidArgumentException
     */
    public function __construct($statusCode, $reasonPhrase = null)
    {
        if (!is_int($statusCode)) {
            throw new InvalidArgumentException('Status code must be an integer');
        }
        if ($statusCode < 100 || $statusCode > 599) {
            throw new InvalidArgumentException('Status code it out of bound');
        }
        $this->statusCode = $statusCode;

        if ($reasonPhrase === null) {
            $reasonPhrase = isset(self::$phraseMap[$statusCode]) ? self::$phraseMap[$statusCode] : '';
        }
        $this->setReasonPhrase($reasonPhrase);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->reasonPhrase === '') {
            return (string) $this->statusCode;
        }
        return $this->statusCode . ' ' . $this->reasonPhrase;
    }

    /**
     * @return bool
     */
    public function isInformational()
    {
        return $this->statusCode < 200;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return bool
     */
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * @return bool
     */
    public function isServerError()
    {
        return $this->statusCode >= 500;
    }

    /**
     * @return bool
     */
    public function isCustom()
    {
        return !isset(self::$phraseMap[$this->statusCode]);
    }

    /**
     * @return bool
     */
    public function isRfc2516()
    {
        return in_array($this->statusCode, self::$rfc2516, true);
    }

    /**
     * @param string $reasonPhrase
     *
     * @throws InvalidArgumentException
     */
    private function setReasonPhrase($reasonPhrase)
    {
        if (!is_string($reasonPhrase)) {
            throw new InvalidArgumentException('Reason phrase must be a string or null');
        }
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @param string $reasonPhrase
     *
     * @return self New instance
     */
    public function changeReasonPhrase($reasonPhrase)
    {
        $cloned = clone $this;
        $cloned->setReasonPhrase($reasonPhrase);

        return $cloned;
    }

    /**
     * @throws RuntimeException
     *
     * @return self New instance
     */
    public function restoreReasonPhraseToDefault()
    {
        $cloned = clone $this;

        $cloned->reasonPhrase = isset(self::$phraseMap[$this->statusCode]) ? self::$phraseMap[$this->statusCode] : '';

        return $cloned;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface New instance
     */
    public function attachToResponse(ResponseInterface $response)
    {
        return $response->withStatus($this->statusCode, $this->reasonPhrase);
    }
}
