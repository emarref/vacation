<?php

namespace Emarref\Vacation;

use Emarref\Vacation\Request\RequestInterface;

interface EngineInterface
{
    /**
     * Convert a request into a response.
     *
     * @param RequestInterface $request
     * @return object
     */
    public function execute(RequestInterface $request);
}
