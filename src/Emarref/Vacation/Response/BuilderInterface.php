<?php

namespace Emarref\Vacation\Response;

use Emarref\Vacation\Request\RequestInterface;

interface BuilderInterface
{
    /**
     * @param RequestInterface $request
     * @param mixed            $content
     * @return ResponseInterface
     */
    public function create(RequestInterface $request, $content);
}
