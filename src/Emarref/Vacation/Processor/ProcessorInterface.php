<?php

namespace Emarref\Vacation\Processor;

use Emarref\Vacation\Metadata\Operation;
use Emarref\Vacation\Request\RequestInterface;

interface ProcessorInterface
{
    /**
     * @param object           $controller
     * @param Operation        $operationMetadata
     * @param RequestInterface $request
     * @return mixed
     */
    public function process($controller, Operation $operationMetadata, RequestInterface $request);
}
