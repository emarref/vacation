<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Metadata;

class Registry
{
    /**
     * @var \ArrayObject
     */
    private $controllers;

    public function __construct()
    {
        $this->controllers = new \ArrayObject();
    }

    /**
     * @param object $controller
     */
    public function registerController($controller)
    {
        $this->controllers->append($controller);
    }

    /**
     * @return \ArrayObject
     */
    public function getControllers()
    {
        return $this->controllers;
    }
}
