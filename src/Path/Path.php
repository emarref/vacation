<?php

namespace Emarref\Vacation\Path;

class Path
{
    /**
     * @var NamedSection[]
     */
    private $sections;

    public function __construct()
    {
        $this->sections = new \ArrayObject();
    }

    public function addSection(NamedSection $section)
    {
        $this->sections[] = $section;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $pathString = '';

        foreach ($this->sections as $section) {
            $pathString .= '/' . $section->getNoun();

            if ($section->isIdentified()) {
                $pathString .= '/{id}';
            }
        }

        return $pathString;
    }
}
