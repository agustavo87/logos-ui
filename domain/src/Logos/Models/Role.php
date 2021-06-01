<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

class Role
{
    public string $code;
    public ?string $label;
    public bool $primary;

    public function __construct($role)
    {
        $this->code = $role->code_name;
        $this->label = $role->label;
        $this->primary = (bool) $role->primary;
    }

    public function __toString()
    {
        return $this->code;
    }
}
