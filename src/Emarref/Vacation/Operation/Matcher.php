<?php

namespace Emarref\Vacation\Operation;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;

class Matcher implements MatcherInterface
{
    /**
     * @param string $method
     * @return string
     */
    protected function normalizeMethod($method)
    {
        return strtoupper($method);
    }

    /**
     * @param RequestInterface   $request
     * @param Metadata\Operation $operationMetadata
     * @return bool
     */
    public function matches(RequestInterface $request, Metadata\Operation $operationMetadata)
    {
        return $this->normalizeMethod($request->getMethod()) === $this->normalizeMethod($operationMetadata->requestMethod);
    }
}
