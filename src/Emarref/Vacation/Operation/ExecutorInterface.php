<?php

namespace Emarref\Vacation\Operation;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;

interface ExecutorInterface
{
    /**
     * @param object             $controller
     * @param Metadata\Operation $operationMetadata
     * @param RequestInterface   $request
     * @return mixed
     */
    public function execute($controller, Metadata\Operation $operationMetadata, RequestInterface $request);
}
