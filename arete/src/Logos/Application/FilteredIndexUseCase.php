<?php

declare(strict_types=1);

namespace Arete\Logos\Application;

use Arete\Exceptions\IncorrectDataStructureException;
use Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase as FilteredIndexUseCaseInterface;
use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;

class FilteredIndexUseCase implements FilteredIndexUseCaseInterface
{
    protected ComplexSourcesRepository $sources;
    protected SourceTypeRepository $sourceTypes;

    public function __construct(
        ComplexSourcesRepository $sources,
        SourceTypeRepository $sourceTypes
    ) {
        $this->sources = $sources;
        $this->sourceTypes = $sourceTypes;
    }

    /**
     * Returns sources filtered by parameters criteria.
     *
     * @param array $params
     *
     * @throws \Arete\Exceptions\IncorrectDataStructureException
     * @return array
     */
    public function __invoke(array $params): array
    {
        return $this->filter($params);
    }

    /**
     * Returns sources filtered by parameters criteria.
     *
     * @param array $params
     *
     * @throws \Arete\Exceptions\IncorrectDataStructureException;
     * @return \Arete\Logos\Domain\Source[]
     */
    public function filter(array $params): array
    {
        $type = $params['type'] ?? null;
        if ($type) {
            if (!$this->validateSourceType($type)) {
                throw new IncorrectDataStructureException('Inexistent Source Type', 22);
            };
        }

        if (isset($params['attributes'])) {
            if (!$this->validateSourceTypeAttributes($params['attributes'], $type)) {
                throw new IncorrectDataStructureException('Inexistent Source Type Attribute', 23);
            };
        }
        return $this->sources->complexFilter($params);
    }

    protected function validateSourceType(string $sourceType): bool
    {
        return in_array($sourceType, $this->sourceTypes->types());
    }

    protected function validateSourceTypeAttributes(array $attributes, $type = null): bool
    {
        foreach ($attributes as $code => $value) {
            if (!in_array($code, $this->sourceTypes->attributes($type))) {
                return false;
            }
        }
        return true;
    }
}
