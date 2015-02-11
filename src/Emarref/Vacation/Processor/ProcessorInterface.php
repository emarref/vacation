<?php

namespace Emarref\Vacation\Processor;

use Emarref\Vacation\Request\Context;
use Emarref\Vacation\Request\RequestInterface;

interface ProcessorInterface
{
    /**
     * @param RequestInterface $request
     * @param Context          $context
     * @return mixed
     */
    public function process(RequestInterface $request, Context $context);
}
