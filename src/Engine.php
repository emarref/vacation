<?php

namespace Emarref\Vacation;

use Emarref\Vacation\Error;
use Emarref\Vacation\Metadata;
use Emarref\Vacation\Controller;
use Emarref\Vacation\Path;
use Emarref\Vacation\Response\FactoryInterface;
use Metadata\MetadataFactory;
use Psr\Http\Message\IncomingRequestInterface;
use Psr\Http\Message\OutgoingResponseInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class Engine
{
    /**
     * @var Path\ResolverInterface;
     */
    private $pathResolver;

    /**
     * @var Controller\Resolver
     */
    private $resourceControllerResolver;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FactoryInterface
     */
    private $responseFactory;

    public function __construct(
        Path\ResolverInterface $pathResolver,
        Controller\Resolver $resourceControllerResolver,
        MetadataFactory $metadataFactory,
        FormFactoryInterface $formFactory,
        FactoryInterface $responseFactory
    ) {
        $this->pathResolver               = $pathResolver;
        $this->resourceControllerResolver = $resourceControllerResolver;
        $this->metadataFactory            = $metadataFactory;
        $this->formFactory                = $formFactory;
        $this->responseFactory            = $responseFactory;
    }

    public function registerResourceController($resourceController)
    {
        $this->resourceControllerResolver->registerResourceController($resourceController);
    }

    /**
     * @param IncomingRequestInterface $request
     * @return object
     * @throws \Exception
     */
    protected function resolveController(IncomingRequestInterface $request)
    {
        $path       = $this->pathResolver->resolveRequest($request);
        $controller = $this->resourceControllerResolver->resolveResourceController($path);

        if (null === $controller) {
            throw new \Exception('Controller not found');
        }

        return $controller;
    }

    /**
     * @param Metadata\Controller      $controllerMetadata
     * @param IncomingRequestInterface $request
     * @return Metadata\Operation
     * @throws \Exception
     */
    protected function resolveOperationMetadata(Metadata\Controller $controllerMetadata, IncomingRequestInterface $request)
    {
        foreach ($controllerMetadata->operations as $operation) {
            if (strtoupper($operation->requestMethod) === $request->getMethod()) {
                return $operation;
            }
        }

        throw new \Exception('Operation not supported.');
    }

    protected function executeOperation(callable $operation, IncomingRequestInterface $request, $arguments)
    {
        try {
            $result = call_user_func_array($operation, $arguments);
        } catch (\Exception $e) {
            return $this->responseFactory->createError($e);
        }

        return $this->responseFactory->create($request, $result);
    }

    /**
     * @param IncomingRequestInterface $request
     * @throws \Exception
     * @return OutgoingResponseInterface
     */
    public function execute(IncomingRequestInterface $request)
    {
        try {
            $controller = $this->resolveController($request);
        } catch (\Exception $e) {
            return $this->responseFactory->createError(new Error\Client('Not Found', 404, $e));
        }

        /** @var Metadata\Controller $controllerMetadata */
        $controllerMetadata = $this->metadataFactory->getMetadataForClass(get_class($controller));

        try {
            $operationMetadata = $this->resolveOperationMetadata($controllerMetadata, $request);
        } catch (\Exception $e) {
            return $this->responseFactory->createError(new Error\Client('Method Not Allowed', 405, $e));
        }

        $arguments = [];

        if ($formFactory = $operationMetadata->formFactory) {
            // TODO Pass identifier to formFactory
            /** @var FormInterface $form */
            $form = $controller->$formFactory($this->formFactory);
            $form->submit($request->getBodyParams(), false);

            if (!$form->isValid()) {
                // TODO Return validation errors
                return $this->responseFactory->createFormError($form);
            }

            $arguments[] = $form;
        }

        if (!empty($operationMetadata->parameters)) {
            $arguments[] = array_intersect_key($request->getQueryParams(), array_flip($operationMetadata->parameters));
        }

        return $this->executeOperation([$controller, $operationMetadata->name], $request, $arguments);
    }
}
