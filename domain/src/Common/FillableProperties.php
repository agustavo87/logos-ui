<?php

declare(strict_types=1);

namespace Arete\Common;

abstract class FillableProperties
{
    use FillsProperties;

    protected $defaultAttributes = [];

    public function __construct(?array $attributes = [])
    {
        $this->fillDefaultsAttributes();
        $this->fill($attributes);
    }

    abstract protected function fillDefaultsAttributes();
}
