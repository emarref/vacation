<?php

namespace Emarref\Vacation\Annotation;

use Emarref\Vacation\Path;

/**
 * @Annotation
 */
class Resource
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->path = $values['value'];
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
