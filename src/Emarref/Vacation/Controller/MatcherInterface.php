<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;

interface MatcherInterface
{
    /**
     * @param RequestInterface $request
     * @param object           $controller
     * @return boolean
     */
    public function matches(RequestInterface $request, $controller);
}
