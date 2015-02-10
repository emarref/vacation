<?php

namespace Emarref\Vacation\Annotation;

/**
 * @Annotation
 */
class Endpoint
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
