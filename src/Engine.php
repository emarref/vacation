<?php

namespace Emarref\Vacation;

use Emarref\Vacation\Error;
use Emarref\Vacation\Metadata;
use Emarref\Vacation\Controller;
use Emarref\Vacation\Path;
use Emarref\Vacation\Response;
use Metadata\MetadataFactory;
use Psr\Http\Message\IncomingRequestInterface;
use Psr\Http\Message\OutgoingResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormInterface;

class Engine
{
    const EVENT_RESPONSE_ADJUST = 'vacation.response.adjust';

    /**
     * @var Controller\RegistryInterface
     */
    private $controllerRegistry;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var Response\FactoryInterface
     */
    private $responseFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param Controller\Registry       $controllerRegistry
     * @param MetadataFactory           $metadataFactory
     * @param Response\FactoryInterface $responseFactory
     * @param EventDispatcherInterface  $dispatcher
     */
    public function __construct(
        Controller\Registry $controllerRegistry,
        MetadataFactory $metadataFactory,
        Response\FactoryInterface $responseFactory,
        EventDispatcherInterface $dispatcher
    ) {
        $this->controllerRegistry = $controllerRegistry;
        $this->metadataFactory    = $metadataFactory;
        $this->responseFactory    = $responseFactory;
        $this->dispatcher         = $dispatcher;
    }

    /**
     * @param object $controller
     */
    public function registerController($controller)
    {
        $this->controllerRegistry->registerController($controller);
    }

    /**
     * @param IncomingRequestInterface $request
     * @return object
     * @throws \Exception
     */
    protected function resolveController(IncomingRequestInterface $request)
    {
        $controller = $this->controllerRegistry->resolveController($request);

        if (null === $controller) {
            throw new \Exception('Controller not found');
        }

        return $controller;
    }

    /**
     * @param object                   $controller
     * @param IncomingRequestInterface $request
     * @return Metadata\Operation
     * @throws \Exception
     */
    protected function resolveOperationMetadata($controller, IncomingRequestInterface $request)
    {
        /** @var Metadata\Resource $resourceMetadata */
        $resourceMetadata = $this->metadataFactory->getMetadataForClass(get_class($controller));

        foreach ($resourceMetadata->operations as $operationMetadata) {
            if (strtoupper($operationMetadata->requestMethod) === $request->getMethod()) {
                return $operationMetadata;
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

        try {
            $operationMetadata = $this->resolveOperationMetadata($controller, $request);
        } catch (\Exception $e) {
            return $this->responseFactory->createError(new Error\Client('Method Not Allowed', 405, $e));
        }

        $arguments = [];

        if ($formFactory = $operationMetadata->formFactory) {
            $form = call_user_func_array([$controller, $formFactory], [$request->getAttributes()]);

            if (!$form instanceof FormInterface) {
                return $this->responseFactory->createError(
                    new Error\Server('Form factory must return an instance of FormInterface', 500)
                );
            }

            $form->submit($request->getBodyParams(), false);

            if (!$form->isValid()) {
                return $this->responseFactory->createFormError($form);
            }

            $arguments[] = $form;
        }

        if (!empty($operationMetadata->parameters)) {
            $arguments[] = array_intersect_key($request->getQueryParams(), array_flip($operationMetadata->parameters));
        }

        $response = $this->executeOperation([$controller, $operationMetadata->name], $request, $arguments);

        $adjustment = new Response\Adjustment();
        $this->dispatcher->dispatch(self::EVENT_RESPONSE_ADJUST, new GenericEvent($adjustment));
        $adjustment->adjust($response);

        return $response;
    }
}
