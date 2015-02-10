<?php

namespace Emarref\Vacation\Validation;

use Emarref\Vacation\Metadata\Operation;
use Emarref\Vacation\Operation\ArgumentBag;
use Emarref\Vacation\Request\RequestInterface;

interface ValidationPassInterface
{
    /**
     * @param RequestInterface $request
     * @param object           $controller
     * @param Operation        $operationMetadata
     * @param ArgumentBag      $operationArguments
     */
    public function validate(RequestInterface $request, $controller, Operation $operationMetadata, ArgumentBag $operationArguments);
}
