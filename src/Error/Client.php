<?php

namespace Emarref\Vacation\Error;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class Client extends \Exception
{
    /**
     * @var string
     * @Serializer\Expose()
     */
    protected $message;

    /**
     * @var int
     * @Serializer\Expose()
     */
    protected $code;
}
