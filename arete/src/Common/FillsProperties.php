<?php

declare(strict_types=1);

namespace Arete\Common;

trait FillsProperties
{
    /**
     * Fill via array object properties and merges
     * with default properties declared in 'defaultPropeties' field.
     *
     * @param array $properties
     *
     * @return void
     */
    public function fill(array $properties)
    {
        $properties = $this->mergeIfDefaults($properties);
        $this->simpleFill($properties);
    }

    /**
     * Fills object properties without merging with defaults.
     *
     * @param array $properties
     *
     * @return void
     */
    public function simpleFill(array $properties)
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    public function mergeIfDefaults($properties)
    {
        if ((!isset($this->defaultProperties)) || !$this->defaultProperties) {
            return $properties;
        }

        return array_merge($this->defaultProperties, $properties);
    }
}
