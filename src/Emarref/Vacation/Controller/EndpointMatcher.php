<?php

namespace Emarref\Vacation\Controller;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Request\RequestInterface;

class EndpointMatcher implements EndpointMatcherInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * @param RequestInterface  $request
     * @param Metadata\Endpoint $endpointMetadata
     * @return boolean
     */
    public function matches(RequestInterface $request, Metadata\Endpoint $endpointMetadata)
    {
        $pathSections = explode('/', trim($endpointMetadata->path, '/'));

        // Substitute parameter placeholders with their values to match request URI
        foreach ($pathSections as &$section) {
            if (0 === strpos($section, ':')) {
                $parameterName = substr($section, 1);
                if ($parameter = $request->getParameter($parameterName)) {
                    $section = $parameter;
                } else {
                    return false;
                }
            }
        }

        $controllerPath = trim(implode('/', $pathSections), '/');
        $requestPath    = trim(parse_url($request->getUrl(), PHP_URL_PATH), '/');

        if (trim(sprintf('%s/%s', trim($this->prefix, '/'), $controllerPath), '/') === $requestPath) {
            return true;
        }

        return false;
    }
}
