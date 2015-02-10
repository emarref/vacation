<?php

namespace Emarref\Vacation\Processor;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;

class PayloadProcessor implements ProcessorInterface
{
    /**
     * @param object             $controller
     * @param Metadata\Operation $operationMetadata
     * @param RequestInterface   $request
     * @return mixed
     */
    public function process($controller, Metadata\Operation $operationMetadata, RequestInterface $request)
    {
        $arguments = [
            $request->getPayloadAsArray(),
        ];

        return $operationMetadata->invoke($controller, $arguments);
    }
}
