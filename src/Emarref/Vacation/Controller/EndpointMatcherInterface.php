<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;

interface EndpointMatcherInterface
{
    /**
     * @param RequestInterface  $request
     * @param Metadata\Endpoint $endpointMetadata
     * @return boolean
     */
    public function matches(RequestInterface $request, Metadata\Endpoint $endpointMetadata);
}
