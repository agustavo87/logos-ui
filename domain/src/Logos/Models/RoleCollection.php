<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Logos\Models\Traits\ExposeAttributes;

abstract class RoleCollection
{
    use ExposeAttributes;

    protected SourceType $type;

    public function type(): SourceType
    {
        return $this->type;
    }
}
