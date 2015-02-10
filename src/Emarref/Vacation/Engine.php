<?php

namespace Emarref\Vacation;

use Emarref\Vacation\Error;
use Emarref\Vacation\Metadata;
use Emarref\Vacation\Controller;
use Emarref\Vacation\Operation;
use Emarref\Vacation\Request\RequestInterface;
use Emarref\Vacation\Response;
use Emarref\Vacation\Validation;

class Engine implements EngineInterface
{
    /**
     * @var Operation\Resolver
     */
    private $operationResolver;

    /**
     * @var Response\BuilderInterface
     */
    private $responseBuilder;

    /**
     * @var Operation\ExecutorInterface
     */
    private $executor;

    /**
     * @var Controller\Resolver
     */
    private $controllerResolver;

    /**
     * @param Controller\Resolver         $controllerResolver
     * @param Operation\Resolver          $operationResolver
     * @param Operation\ExecutorInterface $executor
     * @param Response\BuilderInterface   $responseBuilder
     */
    public function __construct(
        Controller\Resolver $controllerResolver,
        Operation\Resolver $operationResolver,
        Operation\ExecutorInterface $executor,
        Response\BuilderInterface $responseBuilder
    ) {
        $this->controllerResolver = $controllerResolver;
        $this->operationResolver  = $operationResolver;
        $this->executor           = $executor;
        $this->responseBuilder    = $responseBuilder;
    }

    /**
     * @param RequestInterface $request
     * @return object
     * @throws \Exception
     */
    protected function resolveController(RequestInterface $request)
    {
        $controller = $this->controllerResolver->resolve($request);

        if (null === $controller) {
            throw new \Exception('Controller not found');
        }

        return $controller;
    }

    /**
     * @param object           $controller
     * @param RequestInterface $request
     * @return Metadata\Operation
     * @throws \Exception
     */
    protected function resolveOperationMetadata($controller, RequestInterface $request)
    {
        $operationMetadata = $this->operationResolver->resolve($controller, $request);

        if (null === $operationMetadata) {
            throw new \Exception('Operation not supported');
        }

        return $operationMetadata;
    }

    /**
     * @param RequestInterface $request
     * @throws \Exception
     * @return Response\ResponseInterface
     */
    public function execute(RequestInterface $request)
    {
        // Determine the controller/endpoint based on the URL
        try {
            $controller = $this->resolveController($request);
        } catch (\Exception $e) {
            return $this->responseBuilder->create(
                $request,
                new Error\Client('Not Found', Response\ResponseInterface::STATUS_NOT_FOUND, $e)
            );
        }

        // Determine which operation to perform on the endpoint controller based on request method
        try {
            $operationMetadata = $this->resolveOperationMetadata($controller, $request);
        } catch (\Exception $e) {
            return $this->responseBuilder->create(
                $request,
                new Error\Client('Method Not Allowed', Response\ResponseInterface::STATUS_METHOD_NOT_ALLOWED, $e)
            );
        }

        try {
            $result = $this->executor->execute($controller, $operationMetadata, $request);
        } catch (\Exception $e) {
            $result = new Error\Server('An unknown error has occurred', Response\ResponseInterface::STATUS_SERVER_ERROR, $e);
        }

        // Return the result decorated as a response
        return $this->responseBuilder->create($request, $result);
    }
}
