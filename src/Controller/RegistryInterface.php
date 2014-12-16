<?php

namespace Emarref\Vacation\Controller;

use Psr\Http\Message\IncomingRequestInterface;

interface RegistryInterface
{
    /**
     * @param object $controller
     */
    public function registerController($controller);

    /**
     * @param IncomingRequestInterface $request
     * @return object|null
     */
    public function resolveController(IncomingRequestInterface $request);
}
