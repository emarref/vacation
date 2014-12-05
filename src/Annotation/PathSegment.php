<?php

namespace Emarref\Vacation\Annotation;

/**
 * @Annotation
 */
class PathSegment
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $identified;

    public function __construct(array $values)
    {
        $this->name       = $values['value'];
        $this->identified = $values['identified'];
    }

    /**
     * @return boolean
     */
    public function isIdentified()
    {
        return $this->identified;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
