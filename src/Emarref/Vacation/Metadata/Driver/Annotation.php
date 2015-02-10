<?php

namespace Emarref\Vacation\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Emarref\Vacation\Annotation\Endpoint;
use Emarref\Vacation\Annotation\Operation;
use Emarref\Vacation\Metadata;
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
     * @throws \InvalidArgumentException
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /** @var Endpoint $resourceAnnotation */
        $resourceAnnotation = $this->reader->getClassAnnotation($class, 'Emarref\\Vacation\\Annotation\\Endpoint');

        if (null === $resourceAnnotation) {
            throw new \InvalidArgumentException(sprintf('Cannot get resource metadata for class "%s".', $class->getName()));
        }

        $controllerMetadata = new Metadata\Controller($class->getName());
        $controllerMetadata->path = $resourceAnnotation->getPath();

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            /** @var Operation $operationAnnotation */
            $operationAnnotation = $this->reader->getMethodAnnotation($method, 'Emarref\\Vacation\\Annotation\\Operation');

            if (null === $operationAnnotation) {
                continue;
            }

            $operationMetadata = new Metadata\Operation($class->getName(), $method->getName());
            $operationMetadata->requestMethod = $operationAnnotation->getRequestMethod();
            $operationMetadata->parameters    = $operationAnnotation->getParameters();
            $operationMetadata->formFactory   = $operationAnnotation->getFormFactory();

            $controllerMetadata->operations[] = $operationMetadata;
        }

        return $controllerMetadata;
    }
}
