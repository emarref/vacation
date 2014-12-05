<?php

namespace Emarref\Vacation\Metadata;

use Emarref\Vacation\Path;
use Metadata\MergeableClassMetadata;

class Controller extends MergeableClassMetadata
{
    /**
     * @var Path\Path
     */
    public $path;

    /**
     * @var Operation[]
     */
    public $operations;
}
