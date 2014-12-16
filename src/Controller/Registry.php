<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Path;
use Emarref\Vacation\Metadata;
use Metadata\MetadataFactoryInterface;
use Psr\Http\Message\IncomingRequestInterface;

class Registry
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \ArrayObject
     */
    private $controllers;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->controllers     = new \ArrayObject();
    }

    /**
     * @param object $controller
     */
    public function registerController($controller)
    {
        $this->controllers->append($controller);
    }

    /**
     * @param IncomingRequestInterface $request
     * @return object|null
     */
    public function resolveController(IncomingRequestInterface $request)
    {
        foreach ($this->controllers as $controller) {
            /** @var Metadata\Resource $resourceMetadata */
            $resourceMetadata = $this->metadataFactory->getMetadataForClass(get_class($controller));

            $pathSections = explode('/', trim($resourceMetadata->path, '/'));

            array_walk($pathSections, function (&$section) use ($request) {
                // Substitute annotated placeholders with their values to match request URI
                if (0 === strpos($section, ':')) {
                    $section = $request->getAttribute(substr($section, 1));
                }
            });

            $controllerPath = trim(implode('/', $pathSections), '/');
            $requestPath    = trim(parse_url($request->getUrl(), PHP_URL_PATH), '/');

            if ($controllerPath === $requestPath) {
                return $controller;
            }
        }

        return null;
    }
}
