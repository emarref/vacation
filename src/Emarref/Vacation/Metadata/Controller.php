<?php

namespace Emarref\Vacation\Metadata;

use Metadata\MergeableClassMetadata;

class Controller extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var Endpoint[]
     */
    public $endpoints;
}
