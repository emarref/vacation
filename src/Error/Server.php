<?php

namespace Emarref\Vacation\Error;

class Server extends \Exception
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
