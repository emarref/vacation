<?php

namespace Emarref\Vacation\Response;

use Psr\Http\Message\IncomingRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface FactoryInterface
{
    /**
     * @param IncomingRequestInterface $request
     * @param mixed                    $content
     * @return ResponseInterface
     */
    public function create(IncomingRequestInterface $request, $content);

    /**
     * @param \Exception $exception
     * @return ResponseInterface
     */
    public function createError(\Exception $exception);
}
