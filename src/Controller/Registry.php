<?php

namespace Emarref\Vacation\Controller;

use ArrayObject;
use IteratorAggregate;

class Registry implements IteratorAggregate
{
    /**
     * @var ArrayObject
     */
    private $controllers;

    public function __construct()
    {
        $this->controllers = new \ArrayIterator();
    }

    /**
     * @param ResourceControllerInterface $resourceController
     */
    public function add(ResourceControllerInterface $resourceController)
    {
        $this->controllers[] = $resourceController;
    }

    /**
     * @return ArrayObject
     */
    public function getIterator()
    {
        return $this->controllers;
    }
}
