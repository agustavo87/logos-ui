<?php

declare(strict_types=1);

namespace Arete\Common;

abstract class FillableProperties
{
    use FillsProperties;

    protected $defaultProperties = [];

    public function __construct(?array $properties = [])
    {
        $this->fillDefaultsAttributes();
        $this->fill($properties);
    }

    abstract protected function fillDefaultsAttributes();
}
