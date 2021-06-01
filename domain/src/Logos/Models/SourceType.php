<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

class SourceType extends Attributes
{
    protected string $code_name;
    protected ?string $label = null;
    protected string $version;
    protected RoleCollection $roles;

    /**
     * @param mixed $sourceType
     * @param mixed $schema
     * @param mixed $attributes
     *
     * @return static
     */
    public function __construct($sourceType, $schema, $attributes, $roles)
    {
        $this->code_name = $sourceType->code_name;
        $this->label = $sourceType->label;
        $this->version = $schema->version;
        foreach ($attributes as $attribute) {
            $this->attributes[$attribute->code_name] = new Attribute($attribute);
        }
        $this->roles = new RoleCollection($roles, $this);
    }

    public function code(): string
    {
        return $this->code_name;
    }

    public function __toString()
    {
        return $this->code();
    }

    public function label(): ?string
    {
        return $this->label;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function roles(): RoleCollection
    {
        return $this->roles;
    }
}
