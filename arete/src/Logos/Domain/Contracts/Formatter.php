<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Contracts;

use Arete\Logos\Domain\Source;

interface Formatter
{
    public function format(Source $source): string;
}
