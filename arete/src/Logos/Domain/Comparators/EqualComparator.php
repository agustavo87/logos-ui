<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Comparators;

class EqualComparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        return 0;
    }
}
