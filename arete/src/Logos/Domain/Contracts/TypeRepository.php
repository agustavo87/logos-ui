<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Contracts;

use Arete\Logos\Domain\Abstracts\Type;

interface TypeRepository
{
    public function get($codeName): Type;
}
