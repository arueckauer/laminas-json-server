<?php

/**
 * @see       https://github.com/laminas/laminas-json-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-json-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-json-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Json\Server;

use Laminas\Json;
use Laminas\Json\Server;
use PHPUnit\Framework\TestCase;
use stdClass;

use function range;

class ErrorTest extends TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp() : void
    {
        $this->error = new Server\Error();
    }

    public function testCodeShouldBeErrOtherByDefault() : void
    {
        $this->assertEquals(Server\Error::ERROR_OTHER, $this->error->getCode());
    }

    public function testSetCodeShouldCastToInteger() : void
    {
        $this->error->setCode('-32700');
        $this->assertEquals(-32700, $this->error->getCode());
    }

    public function testCodeShouldBeLimitedToStandardIntegers() : void
    {
        foreach ([null, true, 'foo', [], new stdClass(), 2.0] as $code) {
            $this->error->setCode($code);
            $this->assertEquals(Server\Error::ERROR_OTHER, $this->error->getCode());
        }
    }

    public function testCodeShouldAllowArbitraryAppErrorCodesInXmlRpcErrorCodeRange() : void
    {
        foreach (range(-32099, -32000) as $code) {
            $this->error->setCode($code);
            $this->assertEquals($code, $this->error->getCode());
        }
    }

    public function arbitraryErrorCodes() : array
    {
        return [
            '1000'  => [1000],
            '404'   => [404],
            '-3000' => [-3000],
        ];
    }

    /**
     * @dataProvider arbitraryErrorCodes
     */
    public function testCodeShouldAllowArbitraryErrorCode($code) : void
    {
        $this->error->setCode($code);
        $this->assertEquals($code, $this->error->getCode());
    }

    public function testMessageShouldBeNullByDefault() : void
    {
        $this->assertNull($this->error->getMessage());
    }

    public function testSetMessageShouldCastToString() : void
    {
        foreach ([true, 2.0, 25] as $message) {
            $this->error->setMessage($message);
            $this->assertEquals((string) $message, $this->error->getMessage());
        }
    }

    public function testSetMessageToNonScalarShouldSilentlyFail() : void
    {
        foreach ([[], new stdClass()] as $message) {
            $this->error->setMessage($message);
            $this->assertNull($this->error->getMessage());
        }
    }

    public function testDataShouldBeNullByDefault() : void
    {
        $this->assertNull($this->error->getData());
    }

    public function testShouldAllowArbitraryData() : void
    {
        foreach ([true, 'foo', 2, 2.0, [], new stdClass()] as $datum) {
            $this->error->setData($datum);
            $this->assertEquals($datum, $this->error->getData());
        }
    }

    public function testShouldBeAbleToCastToArray() : void
    {
        $this->setupError();
        $array = $this->error->toArray();
        $this->validateArray($array);
    }

    public function testShouldBeAbleToCastToJSON() : void
    {
        $this->setupError();
        $json = $this->error->toJSON();
        $this->validateArray(Json\Json::decode($json, Json\Json::TYPE_ARRAY));
    }

    public function testCastingToStringShouldCastToJSON() : void
    {
        $this->setupError();
        $json = $this->error->__toString();
        $this->validateArray(Json\Json::decode($json, Json\Json::TYPE_ARRAY));
    }

    public function setupError() : void
    {
        $this->error->setCode(Server\Error::ERROR_OTHER)
                    ->setMessage('Unknown Error')
                    ->setData(['foo' => 'bar']);
    }

    public function validateArray($error) : void
    {
        $this->assertIsArray($error);
        $this->assertArrayHasKey('code', $error);
        $this->assertArrayHasKey('message', $error);
        $this->assertArrayHasKey('data', $error);

        $this->assertIsInt($error['code']);
        $this->assertIsString($error['message']);
        $this->assertIsArray($error['data']);

        $this->assertEquals($this->error->getCode(), $error['code']);
        $this->assertEquals($this->error->getMessage(), $error['message']);
        $this->assertSame($this->error->getData(), $error['data']);
    }
}
