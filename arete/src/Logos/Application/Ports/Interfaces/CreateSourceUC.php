<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface CreateSourceUC
{
    /**
     * Return presentation data of source types
     *
     * @return \Arete\Logos\Application\DTO\SourceTypePresentation[]
     */
    public function presentSourceTypes(): array;
}
