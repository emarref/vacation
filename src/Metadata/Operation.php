<?php

namespace Emarref\Vacation\Metadata;

use Metadata\MethodMetadata;

class Operation extends MethodMetadata
{
    /**
     * @var string
     */
    public $requestMethod;

    /**
     * @var array
     */
    public $parameters;

    /**
     * @var string
     */
    public $formFactory;

    /**
     * @var array
     */
    public $arguments;
}
