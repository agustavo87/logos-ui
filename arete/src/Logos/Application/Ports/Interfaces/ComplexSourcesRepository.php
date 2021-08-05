<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface ComplexSourcesRepository extends SourcesRepository
{
    /**
     * @param array $params
     *
     * @return \Arete\Logos\Domain\Source[]
     */
    public function complexFilter(array $params): array;
}
