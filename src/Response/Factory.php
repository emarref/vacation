<?php

namespace Emarref\Vacation\Response;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\IncomingRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Emarref\Vacation\Error;

class Factory implements FactoryInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var bool
     */
    private $includeType = false;

    /**
     * @param SerializerInterface      $serializer
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $dispatcher)
    {
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return boolean
     */
    public function isIncludeType()
    {
        return $this->includeType;
    }

    /**
     * @param boolean $includeType
     */
    public function setIncludeType($includeType)
    {
        $this->includeType = $includeType;
    }

    /**
     * @return SerializationContext
     */
    protected function getSerializationContext()
    {
        return (new SerializationContext())->setSerializeNull(true);
    }

    /**
     * @param ResponseInterface $response
     * @param Adjustment        $adjustment
     */
    protected function adjustResponse(ResponseInterface $response, Adjustment $adjustment)
    {
        if (null !== $adjustment->getStatusCode()) {
            $response->setStatusCode($adjustment->getStatusCode());
        }

        $adjustedHeaders = $adjustment->getHeaders();

        if (!empty($adjustedHeaders)) {
            foreach ($adjustedHeaders as $headerName => $headerValue) {
                $response->setHeader($headerName, $headerValue);
            }
        }
    }

    /**
     * @param mixed $content
     * @param string|null $type
     * @return string
     */
    protected function getPayload($content, $type = null)
    {
        $payload = $this->serializer->serialize($content, 'json', $this->getSerializationContext());

        if (null !== $type) {
            $deserializedPayload = $this->serializer->deserialize($payload, 'array', 'json');
            $payload = json_encode([$type => $deserializedPayload]);
        }

        return $payload;
    }

    /**
     * @param int    $statusCode
     * @param string $content
     * @return ResponseInterface
     */
    protected function buildResponse($statusCode, $content = null)
    {
        $response = new \MyResponse();

        $response->setHeader('Content-Type', 'application/json');
        $response->setStatusCode($statusCode);

        if (null !== $content) {
            $body = new Message($content);
            $response->setBody($body);
        }

        return $response;
    }

    /**
     * @param \Exception $exception
     * @return \MyResponse|ResponseInterface
     */
    public function createError(\Exception $exception)
    {
        return $this->buildResponse($exception->getCode(), $this->getPayload(['messages' => [$exception->getMessage()]], 'error'));
    }

    /**
     * @param IncomingRequestInterface $request
     * @param mixed                    $content
     * @return ResponseInterface
     */
    public function create(IncomingRequestInterface $request, $content = null)
    {
        if (null === $content) {
            $statusCode = 204;
        } else {
            switch ($request->getMethod()) {
                case 'POST':
                    $statusCode = 201;
                    break;
                case 'DELETE':
                    $statusCode = 204;
                    break;
                case 'PUT':
                case 'PATCH':
                case 'GET':
                default:
                    $statusCode = 200;
                    break;
            }

            $content  = $this->getPayload($content, $this->isIncludeType() ? $request->getPathParams()['type'] : null);
        }

        $response = $this->buildResponse($statusCode, $content);

        $adjustment = new Adjustment();
        $this->dispatcher->dispatch('vacation.response.adjust', new GenericEvent($adjustment));
        $this->adjustResponse($response, $adjustment);

        return $response;
    }
}
