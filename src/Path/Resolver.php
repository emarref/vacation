<?php

namespace Emarref\Vacation\Path;

use Psr\Http\Message\IncomingRequestInterface;

class Resolver implements ResolverInterface
{
    /**
     * @param IncomingRequestInterface $request
     * @return Path
     */
    public function resolveRequest(IncomingRequestInterface $request)
    {
        $path = new Path();
        $pathParameters = $request->getAttributes();

        if (!empty($pathParameters['parent_type'])) {
            $isIdentified = !empty($pathParameters['parent_id']);
            $path->addSection(new NamedSection($pathParameters['parent_type'], $isIdentified));
        }

        if (!empty($pathParameters['type'])) {
            $isIdentified = !empty($pathParameters['id']);
            $path->addSection(new NamedSection($pathParameters['type'], $isIdentified));
        }

        if (!empty($pathParameters['action'])) {
            $path->addSection(new NamedSection($pathParameters['action']));
        }

        return $path;
    }
}
