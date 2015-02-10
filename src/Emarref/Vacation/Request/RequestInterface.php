<?php

namespace Emarref\Vacation\Request;

interface RequestInterface
{
    const HEADER_ACCEPT = 'Accept';

    const METHOD_DELETE  = 'DELETE';
    const METHOD_GET     = 'GET';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_PATCH   = 'PATCH';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $attribute
     * @return mixed
     */
    public function getAttribute($attribute);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return array
     */
    public function getQueryParameters();

    /**
     * @return array
     */
    public function getPayloadAsArray();

    /**
     * @param string $header
     * @param mixed  $default
     * @return mixed
     */
    public function getHeader($header, $default = null);
}
