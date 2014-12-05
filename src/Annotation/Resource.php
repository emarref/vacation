<?php

namespace Emarref\Vacation\Annotation;

/**
 * @Annotation
 */
class Resource
{
    /**
     * @var PathSegment[]
     */
    private $pathSegments;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->pathSegments = $values['value'];
    }

    /**
     * @return PathSegment[]
     */
    public function getPathSegments()
    {
        return $this->pathSegments;
    }
}
