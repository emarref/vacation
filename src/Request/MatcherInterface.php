<?php

namespace Emarref\Vacation\Request;

use Emarref\Vacation\Metadata;
use Psr\Http\Message\IncomingRequestInterface;

interface MatcherInterface
{
    /**
     * @param Metadata\Resource $resourceMetadata
     * @return boolean
     */
    public function matches(Metadata\Resource $resourceMetadata);
}
