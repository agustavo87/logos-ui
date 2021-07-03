<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase as FilteredIndexUseCaseInterface;
use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;

class FilteredIndexUseCase implements FilteredIndexUseCaseInterface
{
    protected ComplexSourcesRepository $sources;

    public function __construct(ComplexSourcesRepository $sources)
    {
        $this->sources = $sources;
    }
    public function __invoke(array $params): array
    {
        return $this->filter($params);
    }

    /**
     * @param array $params
     *
     * @return \Arete\Logos\Domain\Source[]
     */
    public function filter(array $params): array
    {
        return $this->sources->complexFilter($params);
    }
}
