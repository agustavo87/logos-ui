<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

class Attributes
{
    protected $attributes = [];

    public function __get($name)
    {
        return $this->attributes[$name] ? $this->attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        return $this->attributes[$name] = $value;
    }

    public function count()
    {
        return  count($this->attributes);
    }

    public function attributes()
    {
        return array_keys($this->attributes);
    }
}
