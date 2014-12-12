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
     * @param object $resourceController
     */
    public function add($resourceController)
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
