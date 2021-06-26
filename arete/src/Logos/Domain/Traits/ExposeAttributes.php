<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Traits;

trait ExposeAttributes
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

    /**
     * The amount of attributes
     *
     * @return int
     */
    public function count(): int
    {
        return  count($this->attributes);
    }

    /**
     * Returns true if the class has the attribute
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function has(string $attribute): bool
    {
        return array_key_exists($attribute, $this->attributes);
    }

    /**
     * Returns the attributes names
     *
     * @return string[]
     */
    public function attributes(): array
    {
        return array_keys($this->attributes);
    }

    /**
     * Returns the attributes as associative attribute => value array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Introduces a new attribute
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return void
     */
    public function pushAttribute(string $attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }
}
