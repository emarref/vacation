<?php

namespace Emarref\Vacation\Request;

use Emarref\Vacation\Metadata;
use Psr\Http\Message\IncomingRequestInterface;

class Matcher implements MatcherInterface
{
    /**
     * @var IncomingRequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param IncomingRequestInterface $request
     * @param string                   $prefix
     */
    public function __construct(IncomingRequestInterface $request, $prefix = '')
    {
        $this->request = $request;
    }

    /**
     * @param Metadata\Resource $resourceMetadata
     * @return boolean
     */
    public function matches(Metadata\Resource $resourceMetadata)
    {
        $request = $this->request;
        $pathSections = explode('/', trim($resourceMetadata->path, '/'));

        // Substitute annotated placeholders with their values to match request URI
        array_walk($pathSections, function (&$section) use ($request) {
            if (0 === strpos($section, ':')) {
                $section = $request->getAttribute(substr($section, 1));
            }
        });

        $controllerPath = trim(implode('/', $pathSections), '/');
        $requestPath    = trim(parse_url($request->getUrl(), PHP_URL_PATH), '/');

        if (trim(sprintf('%s/%s', trim($this->prefix, '/') , $controllerPath), '/') === $requestPath) {
            return true;
        } else {
            return false;
        }
    }
}
