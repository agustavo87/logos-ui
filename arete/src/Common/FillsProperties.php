<?php

declare(strict_types=1);

namespace Arete\Common;

trait FillsProperties
{
    public function fill(array $attributes)
    {
        $attributes = $this->mergeIfDefaults($attributes);
        foreach ($attributes as $property => $value) {
            $this->$property = $value;
        }
    }

    public function mergeIfDefaults($attributes)
    {
        if ((!isset($this->defaultAttributes)) || !$this->defaultAttributes) {
            return $attributes;
        }

        return array_merge($this->defaultAttributes, $attributes);
    }
}
