<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Traits\ExposeAttributes;

abstract class SourceType
{
    use ExposeAttributes;

    protected string $code_name;
    protected ?string $label = null;
    protected string $version;
    protected RoleCollection $roles;

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
