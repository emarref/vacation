<?php

namespace Emarref\Vacation\Processor;

use Emarref\Vacation\Request\Context;
use Emarref\Vacation\Request\RequestInterface;

class DefaultProcessor implements ProcessorInterface
{
    use ParameterableProcessor;

    /**
     * @param RequestInterface $request
     * @param Context          $context
     * @return mixed
     */
    public function process(RequestInterface $request, Context $context)
    {
        $arguments = [
            $this->getParameters($request, $context)
        ];

        return $context->getOperationMetadata()->invoke($context->getController(), $arguments);
    }
}
