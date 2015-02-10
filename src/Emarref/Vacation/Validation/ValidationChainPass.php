<?php

namespace Emarref\Vacation\Validation;

use Emarref\Vacation\Metadata\Operation;
use Emarref\Vacation\Request\RequestInterface;

class ValidationChainPass implements ValidationPassInterface
{
    /**
     * @var \ArrayObject
     */
    private $passes;

    public function __construct()
    {
        $this->passes = new \ArrayObject();
    }

    /**
     * @param ValidationPassInterface $pass
     */
    public function registerValidationPass(ValidationPassInterface $pass)
    {
        $this->passes->append($pass);
    }

    /**
     * @param RequestInterface $request
     * @param object           $controller
     * @param Operation        $operationMetadata
     */
    public function validate(RequestInterface $request, $controller, Operation $operationMetadata)
    {
        // Todo merge multiple pass results
        foreach ($this->passes->getIterator() as $pass) {
            /** @var ValidationPassInterface $pass */
            $pass->validate($request, $controller, $operationMetadata);
        }
    }
}
