<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Abstracts;

use Arete\Logos\Application\Interfaces\MapsSourceTypeLabels as MapsSourceTypeLabelsInterface;

abstract class MapsSourceTypeLabels implements MapsSourceTypeLabelsInterface
{
    protected array $map = [];
    protected string $default = '';

    public function __invoke(string $codeName)
    {
        return $this->mapSourceTypeLabel($codeName);
    }

    public function mapSourceTypeLabel(string $codeName): ?string
    {
        return $this->map[$codeName] ?? $this->default;
    }
}
