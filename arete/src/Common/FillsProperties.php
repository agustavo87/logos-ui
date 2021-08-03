<?php

declare(strict_types=1);

namespace Arete\Common;

trait FillsProperties
{
    public function fill(array $properties)
    {
        /**
         * @todo revisar que no se use el fill para rellenar propiedades individuales
         * y que por error no se termine sobreescribiendo innintencionadamente propiedades
         * anteriores debido a la combinación con valores por defecto.
         */
        $properties = $this->mergeIfDefaults($properties);
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
