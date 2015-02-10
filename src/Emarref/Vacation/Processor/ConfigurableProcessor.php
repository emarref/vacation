<?php

namespace Emarref\Vacation\Processor;

use Emarref\Vacation\Metadata\Operation;
use Emarref\Vacation\Request\RequestInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class ConfigurableProcessor implements ProcessorInterface, ConfigurationInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param object           $controller
     * @param Operation        $operationMetadata
     * @param RequestInterface $request
     * @return mixed
     */
    abstract protected function doProcess($controller, Operation $operationMetadata, RequestInterface $request);

    /**
     * @param array $config
     */
    public function configure(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param object           $controller
     * @param Operation        $operationMetadata
     * @param RequestInterface $request
     * @return mixed
     */
    final public function process($controller, Operation $operationMetadata, RequestInterface $request)
    {
        $result = $this->doProcess($controller, $operationMetadata, $request);
        $this->config = null;
        return $result;
    }
}
