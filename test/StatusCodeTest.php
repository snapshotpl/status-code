<?php

namespace Snapshotpl\StatusCode\Test;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Snapshotpl\StatusCode\StatusCode;
use Zend\Diactoros\Response;

/**
 * StatusCodeTest
 *
 * @author Witold Wasiczko <witold@wasiczko.pl>
 */
class StatusCodeTest extends PHPUnit_Framework_TestCase
{
    public function testConstructOkStatusCode()
    {
        $statusCode = new StatusCode(200);

        $this->assertSame(200, $statusCode->getStatusCode());
    }

    public function testConstructOkStatusCodeWithCorrectReasonPhrase()
    {
        $statusCode = new StatusCode(200);

        $this->assertSame('OK', $statusCode->getReasonPhrase());
    }

    public function testConstructOkStatusCodeWithNullReasonPhrase()
    {
        $statusCode = new StatusCode(200, null);

        $this->assertSame('OK', $statusCode->getReasonPhrase());
    }

    public function testConstructOkStatusCodeWithCustomReasonPhrase()
    {
        $statusCode = new StatusCode(200, 'All Right');

        $this->assertSame('All Right', $statusCode->getReasonPhrase());
    }

    public function testConstructOkStatusCodeWithEmptyReasonPhrase()
    {
        $statusCode = new StatusCode(200, '');

        $this->assertSame('', $statusCode->getReasonPhrase());
    }

    public function testConstructCustomStatusCodeWithoutReasonPhrase()
    {
        $statusCode = new StatusCode(199);

        $this->assertSame('', $statusCode->getReasonPhrase());
    }

    public function testConstructCustomStatusCodeWithReasonPhrase()
    {
        $statusCode = new StatusCode(199, 'Custom Phrase');

        $this->assertSame('Custom Phrase', $statusCode->getReasonPhrase());
    }

    /**
     * @dataProvider providerTryConstructWithInvalidStatusCode
     */
    public function testTryConstructWithInvalidStatusCode($value)
    {
        $this->setExpectedException(InvalidArgumentException::class);

        new StatusCode($value);
    }

    public function providerTryConstructWithInvalidStatusCode()
    {
        return [
            ['200'],
            [''],
            [null],
            [200.5],
            [200.0],
            [99],
            [600],
        ];
    }

    /**
     * @dataProvider providerTryConstructWithInvalidReasonPhrase
     */
    public function testTryConstructWithInvalidReasonPhrase($value)
    {
        $this->setExpectedException(InvalidArgumentException::class);

        new StatusCode(200, $value);
    }

    public function providerTryConstructWithInvalidReasonPhrase()
    {
        return [
            [123],
            [123.34],
        ];
    }

    public function testCastToString()
    {
        $statusCode = new StatusCode(200);

        $this->assertSame('200 OK', (string) $statusCode);
    }

    public function testCastToStringWithoutStatusCode()
    {
        $statusCode = new StatusCode(200, '');

        $this->assertSame('200', (string) $statusCode);
    }

    public function testIsInformational()
    {
        $this->assertTrue((new StatusCode(100))->isInformational());
        $this->assertTrue((new StatusCode(102))->isInformational());
        $this->assertTrue((new StatusCode(199))->isInformational());
        $this->assertFalse((new StatusCode(200))->isInformational());
    }

    public function testIsSuccess()
    {
        $this->assertFalse((new StatusCode(199))->isSuccess());
        $this->assertTrue((new StatusCode(200))->isSuccess());
        $this->assertTrue((new StatusCode(205))->isSuccess());
        $this->assertTrue((new StatusCode(299))->isSuccess());
        $this->assertFalse((new StatusCode(300))->isSuccess());
    }

    public function testIsRedirection()
    {
        $this->assertFalse((new StatusCode(299))->isRedirection());
        $this->assertTrue((new StatusCode(300))->isRedirection());
        $this->assertTrue((new StatusCode(305))->isRedirection());
        $this->assertTrue((new StatusCode(399))->isRedirection());
        $this->assertFalse((new StatusCode(400))->isRedirection());
    }

    public function testIsClientError()
    {
        $this->assertFalse((new StatusCode(399))->isClientError());
        $this->assertTrue((new StatusCode(400))->isClientError());
        $this->assertTrue((new StatusCode(405))->isClientError());
        $this->assertTrue((new StatusCode(499))->isClientError());
        $this->assertFalse((new StatusCode(500))->isClientError());
    }

    public function testIsServerError()
    {
        $this->assertFalse((new StatusCode(499))->isServerError());
        $this->assertTrue((new StatusCode(500))->isServerError());
        $this->assertTrue((new StatusCode(505))->isServerError());
        $this->assertTrue((new StatusCode(599))->isServerError());
    }

    public function testIsCustom()
    {
        $this->assertFalse((new StatusCode(200))->isCustom());
        $this->assertTrue((new StatusCode(599))->isCustom());
    }

    public function testIsRfc2516()
    {
        $this->assertTrue((new StatusCode(200))->isRfc2516());
        $this->assertFalse((new StatusCode(102))->isRfc2516());
    }

    public function testChangeReasonPhraseCreateStatusCode()
    {
        $statusCode = new StatusCode(200);

        $this->assertInstanceOf(StatusCode::class, $statusCode->changeReasonPhrase('All right'));
    }

    public function testChangeReasonPhraseCreateNotSameInstance()
    {
        $statusCode = new StatusCode(200);

        $this->assertNotSame($statusCode, $statusCode->changeReasonPhrase('All right'));
    }

    public function testChangeReasonPhrase()
    {
        $statusCode = new StatusCode(200);
        $newStatusCode = $statusCode->changeReasonPhrase('All right');
        $this->assertSame('All right', $newStatusCode->getReasonPhrase());
    }

    public function testRestoreReasonPhraseToDefault()
    {
        $statusCode = new StatusCode(200, 'All Right');
        $newStatusCode = $statusCode->restoreReasonPhraseToDefault();

        $this->assertInstanceOf(StatusCode::class, $newStatusCode);
        $this->assertSame('OK', $newStatusCode->getReasonPhrase());
        $this->assertNotSame($statusCode, $statusCode->restoreReasonPhraseToDefault());
    }

    public function testRestoreReasonPhraseOfCustomStatusCodeToDefault()
    {
        $statusCode = new StatusCode(599, 'Custom Reason');
        $newStatusCode = $statusCode->restoreReasonPhraseToDefault();

        $this->assertInstanceOf(StatusCode::class, $newStatusCode);
        $this->assertSame('', $newStatusCode->getReasonPhrase());
        $this->assertNotSame($statusCode, $statusCode->restoreReasonPhraseToDefault());
    }

    public function testSetToResponse()
    {
        $response = new Response();

        $statusCode = new StatusCode(599, 'Custom Reason');
        $newResponse = $statusCode->attachToResponse($response);

        $this->assertSame(599, $newResponse->getStatusCode());
        $this->assertSame('Custom Reason', $newResponse->getReasonPhrase());
    }

    public function testCreateFromResponse()
    {
        $response = new Response();

        $statusCode = StatusCode::createFromResponse($response);

        $this->assertSame('200 OK', (string) $statusCode);
    }
}
