<?php

namespace Emarref\Vacation\Processor;

use Emarref\Vacation\Metadata\Operation;
use Emarref\Vacation\Request\RequestInterface;

class DefaultProcessor implements ProcessorInterface
{
    /**
     * @param object           $controller
     * @param Operation        $operationMetadata
     * @param RequestInterface $request
     * @return mixed
     */
    public function process($controller, Operation $operationMetadata, RequestInterface $request)
    {
        $arguments = [];

        if (!empty($operationMetadata->parameters)) {
            // Pluck whitelisted parameters from the request to pass to the operation as arguments
            $queryParameters = array_intersect_key($request->getQueryParameters(), array_flip($operationMetadata->parameters));
            $arguments[] = $queryParameters;
        }

        return $operationMetadata->invoke($controller, $arguments);
    }
}
