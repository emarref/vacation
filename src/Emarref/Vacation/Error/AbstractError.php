<?php

namespace Emarref\Vacation\Error;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractError
{
    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     * @Serializer\Expose()
     */
    protected $message;

    /**
     * @var \Exception
     */
    protected $exception;

    public function __construct($message, $code, $exception)
    {
        $this->message   = $message;
        $this->code      = $code;
        $this->exception = $exception;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
