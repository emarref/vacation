<?php

namespace Emarref\Vacation\Metadata;

use Metadata\MergeableClassMetadata;

class Resource extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var Operation[]
     */
    public $operations;
}
