<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface FilteredIndexUseCase
{
    /**
     * Return an array of sources filtered by parameters.
     *
     * @param array $params
     *
     * @return \Arete\Logos\Domain\Source[]
     */
    public function filter(array $params): array;
}
