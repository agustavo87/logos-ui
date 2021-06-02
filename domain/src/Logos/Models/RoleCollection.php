<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

abstract class RoleCollection extends Attributes
{
    protected SourceType $type;

    public function type(): SourceType
    {
        return $this->type;
    }
}
