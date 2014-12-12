<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Path;
use Emarref\Vacation\Metadata;
use Metadata\MetadataFactoryInterface;

class Resolver
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    public function __construct(Registry $registry, MetadataFactoryInterface $metadataFactory)
    {
        $this->registry        = $registry;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param object $controller
     */
    public function registerResourceController($controller)
    {
        $this->registry->add($controller);
    }

    /**
     * @param Path\Path $path
     * @return object|null
     */
    public function resolveResourceController(Path\Path $path)
    {
        foreach ($this->registry as $controller) {
            /** @var Metadata\Controller $metadata */
            $metadata = $this->metadataFactory->getMetadataForClass(get_class($controller));

            if ((string)$metadata->path === (string)$path) {
                return $controller;
            }
        }
    }
}
