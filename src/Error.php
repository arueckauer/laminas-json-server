<?php

/**
 * @see       https://github.com/laminas/laminas-json-server for the canonical source repository
 * @copyright https://github.com/laminas/laminas-json-server/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-json-server/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Json\Server;

use Laminas\Json\Json;

use function is_bool;
use function is_float;
use function is_numeric;
use function is_scalar;
use function is_string;

class Error
{
    const ERROR_PARSE           = -32700;
    const ERROR_INVALID_REQUEST = -32600;
    const ERROR_INVALID_METHOD  = -32601;
    const ERROR_INVALID_PARAMS  = -32602;
    const ERROR_INTERNAL        = -32603;
    const ERROR_OTHER           = -32000;

    /**
     * Current code
     *
     * @var int
     */
    protected $code = self::ERROR_OTHER;

    /**
     * Error data
     *
     * @var mixed
     */
    protected $data;

    /**
     * Error message
     *
     * @var string
     */
    protected $message;

    /**
     * @param  string $message
     * @param  int $code
     * @param  mixed $data
     */
    public function __construct($message = null, $code = self::ERROR_OTHER, $data = null)
    {
        $this->setMessage($message)
             ->setCode($code)
             ->setData($data);
    }

    /**
     * Set error code.
     *
     * If the error code is 0, it will be set to -32000 (ERROR_OTHER).
     *
     * @param  int $code
     * @return self
     */
    public function setCode($code) : self
    {
        if (! is_scalar($code) || is_bool($code) || is_float($code)) {
            return $this;
        }

        if (is_string($code) && ! is_numeric($code)) {
            return $this;
        }

        $code = (int) $code;

        if (0 === $code) {
            $this->code = self::ERROR_OTHER;
            return $this;
        }

        $this->code = $code;
        return $this;
    }

    /**
     * Get error code
     *
     * @return int|null
     */
    public function getCode() : ?int
    {
        return $this->code;
    }

    /**
     * Set error message
     *
     * @param  string $message
     * @return self
     */
    public function setMessage($message) : self
    {
        if (! is_scalar($message)) {
            return $this;
        }

        $this->message = (string) $message;
        return $this;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * Set error data
     *
     * @param  mixed $data
     * @return self
     */
    public function setData($data) : self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get error data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Cast error to array
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
            'data'    => $this->getData(),
        ];
    }

    /**
     * Cast error to JSON
     *
     * @return string
     */
    public function toJson() : string
    {
        return Json::encode($this->toArray());
    }

    /**
     * Cast to string (JSON)
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->toJson();
    }
}
