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
     * @var Validation\ValidationPassInterface
     */
    private $validator;

    /**
     * @var Controller\Resolver
     */
    private $controllerResolver;

    /**
     * @param Controller\Resolver                $controllerResolver
     * @param Operation\Resolver                 $operationResolver
     * @param Validation\ValidationPassInterface $validator
     * @param Response\BuilderInterface          $responseBuilder
     */
    public function __construct(
        Controller\Resolver $controllerResolver,
        Operation\Resolver $operationResolver,
        Validation\ValidationPassInterface $validator,
        Response\BuilderInterface $responseBuilder
    ) {
        $this->controllerResolver = $controllerResolver;
        $this->operationResolver  = $operationResolver;
        $this->validator          = $validator;
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
     * @param object             $controller
     * @param Metadata\Operation $operationMetadata
     * @param RequestInterface   $request
     * @return Response\ResponseInterface
     */
    protected function executeOperation($controller, Metadata\Operation $operationMetadata, RequestInterface $request)
    {
        // Grab requested parameters from the GET query
        if (!empty($operationMetadata->parameters)) {
            // Pluck requested parameters from the request to pass to the operation as arguments
            $arguments = array_intersect_key($request->getQueryParameters(), array_flip($operationMetadata->parameters));
        } else {
            $arguments = [];
        }

        // Execute the operation method on the endpoint controller
        try {
            $result = $operationMetadata->invoke($controller, [$arguments]);
        } catch (Error\Client $e) {
            return $this->responseBuilder->create($request, $e);
        } catch (Error\Server $e) {
            return $this->responseBuilder->create($request, $e);
        } catch (\Exception $e) {
            return $this->responseBuilder->create(
                $request,
                new Error\Server('An unknown error has occurred', Response\ResponseInterface::STATUS_SERVER_ERROR, $e)
            );
        }

        // Return a response
        return $this->responseBuilder->create($request, $result);
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

        // Ensure the request from the client is valid
        try {
            $this->validator->validate($request, $controller, $operationMetadata);
        } catch (\Exception $e) {
            return $this->responseBuilder->create(
                $request,
                new Error\Client('The request could not be processed', Response\ResponseInterface::STATUS_BAD_REQUEST, $e)
            );
        }

        // Run the operation on the controller
        return $this->executeOperation($controller, $operationMetadata, $request);
    }
}
