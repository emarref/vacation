<?php

namespace Emarref\Vacation\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Emarref\Vacation\Annotation as Vacation;
use Emarref\Vacation\Metadata;
use Emarref\Vacation\Path;
use Metadata\Driver\DriverInterface;

class Annotation implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = new Metadata\Controller($class->getName());

        /** @var Vacation\Resource $resourceAnnotation */
        $resourceAnnotation = $this->reader->getClassAnnotation($class, 'Emarref\\Vacation\\Annotation\\Resource');

        $metadata->path = new Path\Path();

        foreach ($resourceAnnotation->getPathSegments() as $pathSegment) {
            $metadata->path->addSection(new Path\NamedSection($pathSegment->getName(), $pathSegment->isIdentified()));
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            /** @var Vacation\Operation $operationAnnotation */
            $operationAnnotation = $this->reader->getMethodAnnotation($method, 'Emarref\\Vacation\\Annotation\\Operation');

            if (null === $operationAnnotation) {
                continue;
            }

            $operationMetadata = new Metadata\Operation($class->getName(), $method->getName());
            $operationMetadata->requestMethod = $operationAnnotation->getRequestMethod();
            $operationMetadata->parameters    = $operationAnnotation->getParameters();
            $operationMetadata->formFactory   = $operationAnnotation->getFormFactory();

            foreach ($method->getParameters() as $parameter) {
                $operationMetadata->arguments[$parameter->getName()] = $parameter->getClass() ? $parameter->getClass()->getName() : null;
            }

            $metadata->operations[] = $operationMetadata;
        }

        return $metadata;
    }
}
