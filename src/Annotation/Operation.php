<?php

namespace Emarref\Vacation\Annotation;

/**
 * @Annotation
 */
class Operation
{
    /**
     * @var string
     */
    private $requestMethod;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $formFactory;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->requestMethod = $values['value'];

        if (!empty($values['parameters'])) {
            $this->parameters = $values['parameters'];
        }

        if (!empty($values['formFactory'])) {
            $this->formFactory = $values['formFactory'];
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @return string
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }
}
