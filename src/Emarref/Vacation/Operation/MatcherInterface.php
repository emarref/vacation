<?php

namespace Emarref\Vacation\Operation;

use Emarref\Vacation\Metadata\Operation;
use Emarref\Vacation\Request\RequestInterface;

interface MatcherInterface
{
    /**
     * @param RequestInterface $request
     * @param Operation        $operationMetadata
     * @return mixed
     */
    public function matches(RequestInterface $request, Operation $operationMetadata);
}
