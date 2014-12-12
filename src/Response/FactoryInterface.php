<?php

namespace Emarref\Vacation\Response;

use Psr\Http\Message\IncomingRequestInterface;
use Psr\Http\Message\OutgoingResponseInterface;
use Symfony\Component\Form\FormInterface;

interface FactoryInterface
{
    /**
     * @param IncomingRequestInterface $request
     * @param mixed                    $content
     * @return OutgoingResponseInterface
     */
    public function create(IncomingRequestInterface $request, $content);

    /**
     * @param \Exception $exception
     * @return OutgoingResponseInterface
     */
    public function createError(\Exception $exception);

    /**
     * @param FormInterface $form
     * @return OutgoingResponseInterface
     */
    public function createFormError(FormInterface $form);
}
