<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository as SourcesRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Contracts\Formatter;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;

class DBSourcesRepository extends DBRepository implements SourcesRepositoryPort
{
    protected CreatorsRepository $creators;
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected ParticipationRepository $participations;
    protected Schema $schema;
    protected int $maxFetchSize = 30;
    protected array $cache = [];
    protected Formatter $defaultFormatter;

    public function __construct(
        CreatorsRepository $creators,
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        ParticipationRepository $participations,
        Formatter $defaultFormatter,
        Schema $schema,
        DB $db,
        LogosEnviroment $logos
    ) {
        parent::__construct($db, $logos);
        $this->creators = $creators;
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
        $this->participations = $participations;
        $this->defaultFormatter = $defaultFormatter;
        $this->schema = $schema;
    }

    public function createFromArray(array $params, ?string $ownerID = null): Source
    {
        $ownerID = $ownerID ?? $this->logos->getOwner();

        // first, let's create the source and insert it's attributes
        $source = new Source($this->sourceTypes, $this->defaultFormatter);
        $source->fill([
            'typeCode' => $params['type'],
            'ownerID' => $ownerID
        ]);

        $this->db->insertEntityAttributes(
            $source,
            $params['attributes'],
            $ownerID
        );

        // then add the participations
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

        return $source;
    }

    public function get(int $id): Source
    {
        if (array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }
        return $this->getNew($id);
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
        // lets create the source with it's attributes
        $attributes = $this->db->getEntityAttributes($id);
        $sourceEntry = $attributes->first();
        $source = new Source(
            $this->sourceTypes,
            $this->defaultFormatter,
            [
                'id' => $sourceEntry->id,
                'typeCode' => $sourceEntry->source_type_code_name
            ]
        );
        // hidrate the model attributes
        foreach ($attributes as $code => $data) {
            $source->pushAttribute(
                $code,
                $data->value
            );
        }

        // lets add participations in it's creation.
        $participations = new ParticipationSet($source, $this->creators, $this->participations);
        $source->fill([
            'participations' => $participations->load()
        ]);
        return $this->cache[$id] = $source;
    }

    public function save(Source $source): bool
    {
        $source->participations()->save();

        return (bool) $this->db->insertAttributes(
            $source->id(),
            $source->type(),
            $source->getDirtyAttributes()
        );
    }

    public function getLike($attributeCode, $attributeValue, ?int $user = null, $page = null): array
    {
        $user = $user ?? $this->logos->getOwner();
        $entitiesIDs = $this->db->findEntitiesWith('source', $attributeCode, $attributeValue);

        $result = [];
        $take = count($entitiesIDs) > $this->maxFetchSize ? $this->maxFetchSize : count($entitiesIDs);
        for ($i = 0; $i <= $take - 1; $i++) {
            $creator = $this->get($entitiesIDs[$i]);
            $result[] = $creator;
        }

        return $result;
    }
}
