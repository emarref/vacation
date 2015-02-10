<?php

namespace Emarref\Vacation\Operation;

use Emarref\Vacation\Metadata;
use Emarref\Vacation\Processor;
use Emarref\Vacation\Request\RequestInterface;

class Executor implements ExecutorInterface
{
    /**
     * @var \ArrayObject
     */
    private $processors;

    public function __construct()
    {
        $this->processors = new \ArrayObject();
    }

    /**
     * @param string                       $name
     * @param Processor\ProcessorInterface $processor
     */
    public function registerProcessor($name, Processor\ProcessorInterface $processor)
    {
        $this->processors->offsetSet($name, $processor);
    }

    /**
     * @param string $name
     * @throws \InvalidArgumentException
     * @return Processor\ProcessorInterface
     */
    protected function getProcessor($name)
    {
        if (!$this->processors->offsetExists($name)) {
            throw new \InvalidArgumentException(sprintf('Unknown processor "%s".', $name));
        }

        return $this->processors->offsetGet($name);
    }

    /**
     * @param object             $controller
     * @param Metadata\Operation $operationMetadata
     * @param RequestInterface   $request
     * @return mixed
     */
    public function execute($controller, Metadata\Operation $operationMetadata, RequestInterface $request)
    {
        if ($operationMetadata->processor) {
            $processor = $this->getProcessor($operationMetadata->processor->name);
        } else {
            switch ($request->getMethod()) {
                case RequestInterface::METHOD_POST:
                case RequestInterface::METHOD_PUT:
                    $processor = new Processor\PayloadProcessor();
                    break;
                default:
                    $processor = new Processor\DefaultProcessor();
            }
        }

        if ($processor instanceof Processor\ConfigurableProcessor) {
            $configurationProcessor = new \Symfony\Component\Config\Definition\Processor();
            $configuration = $configurationProcessor->processConfiguration($processor, [$operationMetadata->processor->options]);
            $processor->configure($configuration);
        }

        return $processor->process($controller, $operationMetadata, $request);
    }
}
