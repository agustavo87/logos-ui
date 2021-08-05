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
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;

class MemorySourcesRepository implements SourcesRepository, ComplexSourcesRepository
{
    protected CreatorsRepository $creators;
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected ParticipationRepository $participations;
    protected Schema $schema;
    protected Formatter $defaultFormatter;
    protected LogosEnviroment $logos;

    public static array $sources = [];
    public static array $ids = [0];

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

        $key = $this->getKey($params);

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

    protected function getKey(array $params): string
    {
        if (isset($params['key'])) {
            $keyWord = $params['key'];
        } else {
            /** @todo mejorar la búsqueda del apellido o atributo principal del creador principal */
            if (isset($params['participations'][0]['creator']['attributes']['lastName'])) {
                $keyWord = $params['participations'][0]['creator']['attributes']['lastName'];
            } elseif (isset($params['title'])) {
                $keyWord = explode(' ', $params['title'])[0];
            } else {
                $keyWord = 'anon';
            }
            /** @todo acá agregar el año */
        }
        $i = 1;
        $baseKeyWord = $keyWord;
        while ($this->keyExist($keyWord)) {
            $keyWord = $baseKeyWord . ++$i;
        }
        return $keyWord;
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

    public function getByKey(string $key)
    {
        foreach (self::$sources as $id => $source) {
            if ($source->key() == $key) {
                return $source;
            }
        }
        return null;
    }

    public function get(int $id): Source
    {
        return self::$sources[$id];
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
        ?array $subsetIDs = null
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
        return array_values($results);
    }

    public function complexFilter(array $params): array
    {
        /* if there's no sources nothing can be filtered */
        if (!count(self::$sources)) {
            return [];
        }
        $result = [];

        /* filter by source attributes */
        $ownerID = isset($params['ownerID']) ? $params['ownerID'] : null;
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $attribute => $condition) {
                $subset = $this->pluckIds($result);
                $result = $this->getLike(
                    $attribute,
                    $condition,
                    $ownerID,
                    $subset
                );
            }
        }

        /* filter by owner */
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


        /* filter by creators/participants */
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
        return array_values($result);
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
