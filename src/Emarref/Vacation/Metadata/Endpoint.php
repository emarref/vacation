<?php

namespace Emarref\Vacation\Metadata;

use Metadata\MergeableClassMetadata;

class Endpoint extends MergeableClassMetadata
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
