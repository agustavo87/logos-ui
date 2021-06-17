<?php

declare(strict_types=1);

namespace Arete\Common;

trait FillsProperties
{
    public function fill(array $properties)
    {
        $properties = $this->mergeIfDefaults($properties);
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    public function mergeIfDefaults($properties)
    {
        if ((!isset($this->defaultAttributes)) || !$this->defaultAttributes) {
            return $properties;
        }

        return array_merge($this->defaultAttributes, $properties);
    }
}
