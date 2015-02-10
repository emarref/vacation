<?php

namespace Emarref\Vacation\Response;

interface FactoryInterface
{
    /**
     * @return ResponseInterface
     */
    public function get();
}
