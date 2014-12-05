<?php

namespace Emarref\Vacation\Path;

class NamedSection
{
    /**
     * @var string
     */
    public $noun;

    /**
     * @var boolean
     */
    public $identified;

    public function __construct($noun, $identified = false)
    {
        $this->noun       = $noun;
        $this->identified = $identified;
    }

    /**
     * @return boolean
     */
    public function isIdentified()
    {
        return $this->identified;
    }

    /**
     * @param boolean $identified
     */
    public function setIdentified($identified)
    {
        $this->identified = $identified;
    }

    /**
     * @return string
     */
    public function getNoun()
    {
        return $this->noun;
    }

    /**
     * @param string $noun
     */
    public function setNoun($noun)
    {
        $this->noun = $noun;
    }
}
