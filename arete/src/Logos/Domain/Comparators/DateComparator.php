<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Comparators;

use DateTime;

class DateComparator implements ComparatorInterface
{
    public function compare($a, $b): int
    {
        $dateA = $this->datesize($a);
        $dateB = $this->datesize($b);
        return $a <=> $b;
    }

    protected function datesize($date): DateTime
    {
        if ($date instanceof DateTime) {
            return $date;
        }
        return new DateTime($date);
    }
}
