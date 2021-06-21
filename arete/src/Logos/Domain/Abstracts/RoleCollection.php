<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Abstracts;

use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Logos\Domain\Abstracts\SourceType;

abstract class RoleCollection
{
    use ExposeAttributes;

    protected SourceType $type;

    public function type(): SourceType
    {
        return $this->type;
    }
}
