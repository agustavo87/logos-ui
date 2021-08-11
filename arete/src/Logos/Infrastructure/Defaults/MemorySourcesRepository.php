<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Defaults;

use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Contracts\Formatter;
use Arete\Logos\Domain\IndexResults;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;
use Arete\Logos\Domain\SourcesKeyGenerator;

/**
 * Use temporal memory as sources repository. For test purpouses.
 */
class MemorySourcesRepository implements SourcesRepository, ComplexSourcesRepository
{
    use IndexResults;

    protected CreatorsRepository $creators;
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected ParticipationRepository $participations;
    protected Schema $schema;
    protected Formatter $defaultFormatter;
    protected LogosEnviroment $logos;

    public static array $sources = [];
    public static array $ids = [0];

    protected SourcesKeyGenerator $keyGenerator;

    public function __construct(
        CreatorsRepository $creators,
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        ParticipationRepository $participations,
        Formatter $defaultFormatter,
        Schema $schema,
        LogosEnviroment $logos
    ) {
        $this->creators = $creators;
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
        $this->participations = $participations;
        $this->defaultFormatter = $defaultFormatter;
        $this->schema = $schema;
        $this->logos = $logos;
        $this->keyGenerator = new SourcesKeyGenerator($this, $creators);
    }

    public function flush()
    {
        self::$sources = [];
    }

    protected static function newId(): int
    {
        $id = array_slice(self::$ids, -1, 1)[0] + 1;
        self::$ids[] = $id;
        return $id;
    }

    public function createFromArray(array $params, $ownerID = null): Source
    {
        $ownerID = $ownerID ?? $this->logos->getOwner();
        $entityID = $this->newId();

        $key = $this->keyGenerator->getKey($params);

        $source = new Source($this->sourceTypes, $this->defaultFormatter);
        $source->fill([
            'id'        => $entityID,
            'key'       => $key,
            'typeCode'  => $params['type'],
            'ownerID'   => $ownerID
        ]);

        foreach ($params['attributes'] as $code => $value) {
            $source->pushAttribute($code, $value);
        }

        $participations = new ParticipationSet($source, $this->creators, $this->participations);
        if (array_key_exists('participations', $params)) {
            foreach ($params['participations'] as $participationData) {
                $participations->pushNew(
                    $participationData['creator'],
                    $participationData['role'],
                    $participationData['relevance']
                );
            }
        }
        $source->simpleFill([
            'participations' => $participations
        ]);

        return self::$sources[$entityID] = $source;
    }

    public function get(int $id): Source
    {
        return self::$sources[$id];
    }

    public function getByKey(string $key)
    {
        foreach (self::$sources as $id => $source) {
            if ($source->key() == $key) {
                return $source;
            }
        }
        return null;
    }

    public function keyExist(string $key): bool
    {
        foreach (self::$sources as $id => $source) {
            if ($source->key() == $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * Fetch new source from persistence even if it's already feteched
     *
     * This can create parallel version of same entity and have unpredicted results.
     *
     * @param int $id
     *
     * @return Source
     */
    public function getNew(int $id): Source
    {
        return clone self::$sources[$id];
    }

    public function save(Source $source): bool
    {
        self::$sources[$source->id()] = $source;
        return true;
    }

    /**
     *  Give the sources who matches the specified criteria
     *
     * @param string    $attributeCode
     * @param string    $attributeValue
     * @param mixed     $ownerID = null
     *
     * @return Source[]
     */
    public function getLike(
        string $attributeCode,
        string $attributeValue,
        $ownerID = null,
        ?array $subsetIDs = null,
        bool $limit = true
    ): array {
        $results =  array_filter(self::$sources, function (Source $source) use (
            $attributeValue,
            $attributeCode,
            $ownerID,
            $subsetIDs
        ) {
            if ($ownerID) {
                if ($ownerID != $source->ownerID()) {
                    return false;
                }
            }
            if ($subsetIDs) {
                if (!in_array($source->id(), $subsetIDs, true)) {
                    return false;
                }
            }
            return $source->has($attributeCode) ?
                                str_contains((string) $source->$attributeCode, $attributeValue) :
                                false;
        });

        $results = array_values($results);

        if ($limit) {
            $results = $this->limitResult($results);
        }
        return $results;
    }

    public function complexFilter(array $params): array
    {
        /* if there's no sources nothing can be filtered */
        if (!count(self::$sources)) {
            return [];
        }
        $result = [];

        $ownerID = isset($params['ownerID']) ? $params['ownerID'] : null;

        /* Filter by source attributes */
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $attribute => $condition) {
                $subset = $this->pluckIds($result);
                $result = $this->getLike(
                    $attribute,
                    $condition,
                    $ownerID,
                    $subset,
                    false
                );
            }
        }

        /* Filter by owner */
        if (isset($params['ownerID'])) {
            // if there is results, start from there, if not, start with all sources.
            $result = count($result) ? $result : self::$sources;
            $result = array_filter(
                $result,
                fn (Source $source) => (string) $source->ownerID() == $params['ownerID']
            );
        }

        /* Filter by key */
        if (isset($params['key'])) {
            $result = count($result) ? $result : self::$sources;
            $result = array_filter(
                $result,
                fn (Source $source) => (string) str_contains($source->key(), $params['key'])
            );
        }

        /* Filter by creators/participants */
        if (isset($params['participations'])) {
            // if there is results, start from there, if not, start with all sources.
            $result = count($result) ? $result : self::$sources;

            foreach ($params['participations'] as $role => $properties) {
                // filter the sources that have some creator with the specified role.
                $result = array_filter(
                    $result,
                    fn (Source $source) => $source->participations()->has($role)
                );

                // if specified, filter by the creator attributes
                if (isset($properties['attributes'])) {
                    foreach ($properties['attributes'] as $attrCode => $attrValue) {
                        $result = array_filter(
                            $result,
                            function (Source $source) use ($attrCode, $attrValue, $role) {
                                return $this->filterByAttribute(
                                    $source->participations()->$role,
                                    $attrCode,
                                    $attrValue
                                );
                            }
                        );
                    }
                }
            }
        }

        /* Order the results */
        $result = array_values($this->order($result));

        /* Limit the results */
        $result = $this->limitResult($result);

        return $result;
    }

    protected function limitResult(array $result): array
    {
        return array_slice($result, $this->offset, $this->limit);
    }

    /**
     * Order array by Repository parameters.
     *
     * @param   \Arete\Logos\Domain\Source[]   $sources
     *
     * @return  \Arete\Logos\Domain\Source[]
     */
    protected function order(array $sources): array
    {
        switch ($this->orderBy['group']) {
            case 'attributes':
                return $this->orderByAttributes($sources, $this->orderBy['field']);
                break;
            case 'source':
                return $this->orderByProperties($sources, $this->orderBy['field']);
                break;
            case 'creator':
                return $this->orderByCreatorAttributes($sources, $this->orderBy['field']);
            default:
                return $sources;
                break;
        }
    }

    /**
     * @param \Arete\Logos\Domain\Source[] $sources
     * @param string $field
     *
     * @return \Arete\Logos\Domain\Source[]
     */
    protected function orderByProperties(array $sources, string $field): array
    {
        usort(
            $sources,
            fn (Source $a, Source $b) => $a->compareProperty($field, $b)
        );
        return $sources;
    }

    protected function orderByAttributes(array $sources, string $field): array
    {
        usort(
            $sources,
            fn (Source $a, Source $b) => $a->compare($field, $a->$field, $b->$field)
        );

        return $sources;
    }

    protected function orderByCreatorAttributes(array $sources, string $field): array
    {
        usort(
            $sources,
            function (Source $a, Source $b) use ($field) {
                $creatorA = $a->participations()->byRelevance()[0]->creator();
                $creatorB = $b->participations()->byRelevance()[0]->creator();
                return $creatorA->compare($field, $creatorA->$field, $creatorB->$field);
            }
        );
        return $sources;
    }

    /**
     * @param object[] $attributables
     * @param string $attrCode
     * @param string $attrValue
     *
     * @return array
     */
    protected function filterByAttribute(array $attributables, string $attrCode, string $attrValue): array
    {
        return array_filter(
            $attributables,
            function ($attributable) use ($attrCode, $attrValue) {
                if ($attributable->has($attrCode)) {
                    return (string) $attributable->$attrCode == $attrValue;
                }
                return false;
            }
        );
    }

    /**
     * @param object[] $objects
     * @param string $property
     *
     * @return array
     */
    protected function pluck(array $objects, string $property, bool $method = true): array
    {
        return array_map(
            fn ($object) => $method ? $object->$property() : $object->$property,
            $objects
        );
    }

    /**
     * @param Source[] $sources
     *
     * @return int[]|null
     */
    protected function pluckIds(array $sources): ?array
    {
        return count($sources) ? $this->pluck($sources, 'id') : null;
    }
}
