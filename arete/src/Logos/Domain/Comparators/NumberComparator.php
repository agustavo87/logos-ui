<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Comparators;

class NumberComparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        return $a <=> $b;
    }
}
