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
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $resourceAnnotation = $this->reader->getClassAnnotation($class, 'Emarref\\Vacation\\Annotation\\Resource');

        if (null === $resourceAnnotation) {
            throw new \InvalidArgumentException(sprintf('Cannot get resource metadata for class "%s".', $class->getName()));
        }

        $resourceMetadata = new Metadata\Resource($class->getName());
        $resourceMetadata->path = $resourceAnnotation->getPath();

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

            $resourceMetadata->operations[] = $operationMetadata;
        }

        return $resourceMetadata;
    }
}
