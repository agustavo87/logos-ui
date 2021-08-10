<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Comparators;

class TextComparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        return strcmp($a, $b);
    }
}
