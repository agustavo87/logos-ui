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
        if (!$this->defaultAttributes) {
            return $attributes;
        }

        return array_merge($this->defaultAttributes, $attributes);
    }
}
