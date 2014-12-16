<?php

namespace Emarref\Vacation\Response;

use Psr\Http\Message\OutgoingResponseInterface;

class Adjustment
{
    /**
     * @var array
     */
    private static $restrictedHeaders = [
        'content-type',
    ];

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var \ArrayObject
     */
    private $headers;

    public function __construct()
    {
        $this->headers = new \ArrayObject();
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param string $header
     * @return bool
     */
    protected function isRestrictedHeader($header)
    {
        return in_array(strtolower($header), self::$restrictedHeaders);
    }

    /**
     * @param string $header
     * @param mixed $value
     * @return $this
     */
    public function setHeader($header, $value)
    {
        if ($this->isRestrictedHeader($header)) {
            throw new \InvalidArgumentException(sprintf('Header "%s" is restricted.', $header));
        }

        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * @return \ArrayObject
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param OutgoingResponseInterface $response
     */
    public function adjust(OutgoingResponseInterface $response)
    {
        if (null !== $this->getStatusCode()) {
            $response->setStatus($this->getStatusCode());
        }

        $adjustedHeaders = $this->getHeaders();

        if (!empty($adjustedHeaders)) {
            foreach ($adjustedHeaders as $headerName => $headerValue) {
                $response->setHeader($headerName, $headerValue);
            }
        }
    }
}
