<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Common\FillsProperties;
use Arete\Logos\Domain\Abstracts\RoleCollection as AbstractRoleCollection;

class RoleCollection extends AbstractRoleCollection
{
    use FillsProperties;

    public function __construct(array $properties = [])
    {
        $this->fill($properties);
    }
}
