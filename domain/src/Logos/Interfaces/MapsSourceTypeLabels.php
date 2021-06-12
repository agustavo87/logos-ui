<?php

declare(strict_types=1);

namespace Arete\Logos\Interfaces;

/**
 * Maps from a Source Type code name to its label.
 */
interface MapsSourceTypeLabels
{
    public function mapSourceTypeLabel(string $codeName): ?string;
}
