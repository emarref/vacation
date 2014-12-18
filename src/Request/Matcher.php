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

    public function __construct(IncomingRequestInterface $request)
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

        // Return true if the end of the request path matches the controller path.
        if (substr($requestPath, strlen($controllerPath) * -1) === $controllerPath) {
            return true;
        } else {
            return false;
        }
    }
}
