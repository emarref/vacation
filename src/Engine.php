<?php

namespace Emarref\Vacation;

use Emarref\Vacation\Error;
use Emarref\Vacation\Metadata;
use Emarref\Vacation\Operation;
use Emarref\Vacation\Controller;
use Emarref\Vacation\Path;
use Emarref\Vacation\Response\FactoryInterface;
use Metadata\MetadataFactory;
use Psr\Http\Message\IncomingRequestInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

abstract class Engine
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

    public function registerResourceController(Controller\ResourceControllerInterface $resourceController)
    {
        $this->resourceControllerResolver->registerResourceController($resourceController);
    }

    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    /**
     * @param IncomingRequestInterface $request
     * @return Controller\ResourceControllerInterface
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
     * @return ResponseInterface
     */
    public function execute(IncomingRequestInterface $request)
    {
        try {
            $controller = $this->resolveController($request);
        } catch (\Exception $e) {
            return $this->responseFactory->createError(new Error\Client('Not Found', 404));
        }

        /** @var Metadata\Controller $controllerMetadata */
        $controllerMetadata = $this->metadataFactory->getMetadataForClass(get_class($controller));

        try {
            $operationMetadata = $this->resolveOperationMetadata($controllerMetadata, $request);
        } catch (\Exception $e) {
            return $this->responseFactory->createError(new Error\Client('Method Not Alowed', 405));
        }

        $arguments = [];

        if ($formFactory = $operationMetadata->formFactory) {
            // TODO Pass identifier to formFactory
            /** @var FormInterface $form */
            $form = $controller->$formFactory($this->formFactory);
            $form->submit($request->getBodyParams(), false);

            if (!$form->isValid()) {
                return $this->responseFactory->createError(new Error\Client('Bad Request', 400));
            }

            $arguments[] = $form;
        }

        if (!empty($operationMetadata->parameters)) {
            $arguments[] = array_intersect_key($request->getQueryParams(), array_flip($operationMetadata->parameters));
        }

        return $this->executeOperation([$controller, $operationMetadata->name], $request, $arguments);
    }
}
