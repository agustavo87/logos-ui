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

        $source = new Source($this->sourceTypes, $this->defaultFormatter);
        $source->fill([
            'id'        => $entityID,
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
        $source->fill([
            'participations' => $participations
        ]);

        return self::$sources[$entityID] = $source;
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
            return str_contains((string) $source->$attributeCode, $attributeValue);
        });
        return array_values($results);
    }

    public function complexFilter(array $params): array
    {
        $result = [];
        if ($params['attributes']) {
            foreach ($params['attributes'] as $attribute => $condition) {
                $subset = count($result) ? $this->pluck($result, 'id') : null;
                $result = $this->getLike(
                    $attribute,
                    $condition,
                    null,
                    $subset
                );
            }
        }
        return $result;
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
}
