<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Comparators;

use Arete\Exceptions\IncorrectDataStructureException;

class ComparatorFactory
{
    public function get(string $type): ComparatorInterface
    {
        $type = $type == 'default' ? 'text' : $type;
        switch ($type) {
            case 'text':
                return new TextComparator();
                break;
            case 'number':
                return new NumberComparator();
                break;
            case 'date':
                return new DateComparator();
                break;
            case 'complex':
                return new EqualComparator();
                break;
            default:
                throw new IncorrectDataStructureException(
                    "The '{$type}' attribute data type is not recognizable.",
                    24
                );
                break;
        }
    }
}
