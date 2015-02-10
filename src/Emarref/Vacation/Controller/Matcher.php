<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;
use Metadata\MetadataFactoryInterface;

class Matcher implements MatcherInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param string                   $prefix
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, $prefix = '')
    {
        $this->metadataFactory = $metadataFactory;
        $this->prefix = $prefix;
    }

    /**
     * @param RequestInterface $request
     * @param object           $controller
     * @return boolean
     */
    public function matches(RequestInterface $request, $controller)
    {
        /** @var Metadata\Controller $controllerMetadata */
        $controllerMetadata = $this->metadataFactory->getMetadataForClass(get_class($controller));
        $pathSections = explode('/', trim($controllerMetadata->path, '/'));

        // Substitute annotated placeholders with their values to match request URI
        array_walk($pathSections, function (&$section) use ($request) {
            if (0 === strpos($section, ':')) {
                $section = $request->getAttribute(substr($section, 1));
            }
        });

        $controllerPath = trim(implode('/', $pathSections), '/');
        $requestPath    = trim(parse_url($request->getUrl(), PHP_URL_PATH), '/');

        if (trim(sprintf('%s/%s', trim($this->prefix, '/'), $controllerPath), '/') === $requestPath) {
            return true;
        } else {
            return false;
        }
    }
}
