<?php

namespace Emarref\Vacation\Path;

use Psr\Http\Message\IncomingRequestInterface;

interface ResolverInterface
{
    /**
     * @param IncomingRequestInterface $request
     * @return Path
     */
    public function resolveRequest(IncomingRequestInterface $request);
}
