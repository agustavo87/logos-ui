<?php

declare(strict_types=1);

namespace Arete\Logos\Interfaces;

use Arete\Logos\Models\Abstracts\Type;

interface TypeRepository
{
    public function get($codeName): Type;
}
