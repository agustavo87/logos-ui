<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Comparators;

interface ComparatorInterface
{
    public function compare($a, $b): int;
}
