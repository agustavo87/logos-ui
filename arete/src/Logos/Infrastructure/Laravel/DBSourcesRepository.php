<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\SourcesRepository as SourcesRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;

class DBSourcesRepository extends DBRepository implements SourcesRepositoryPort
{
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected Schema $schema;
    protected int $maxFetchSize = 30;

    public function __construct(
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        Schema $schema,
        DB $db
    ) {
        parent::__construct($db);
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
        $this->schema = $schema;
    }

    public function createFromArray(array $params): Source
    {
        $source = new Source($this->sourceTypes);
        $participations = new ParticipationSet($source);
        $source->fill([
            'typeCode' => $params['type'],
            'participations' => $participations,
        ]);
        $this->db->insertEntityAttributes(
            $source,
            $params['attributes'],
            1
        );
        return $source;
    }

    public function get(int $id): Source
    {
        $attributes = $this->db->getEntityAttributes($id);
        $sourceEntry = $attributes->first();
        $source = new Source(
            $this->sourceTypes,
            [
                'id' => $sourceEntry->id,
                'typeCode' => $sourceEntry->source_type_code_name
            ]
        );
        $participations = new ParticipationSet($source);
        $source->fill([
            'participations' => $participations
        ]);

        foreach ($attributes as $code => $data) {
            $source->pushAttribute(
                $code,
                $data->value
            );
        }
        return $source;
    }

    public function save(Source $source): bool
    {
        return (bool) $this->db->insertAttributes(
            $source->id(),
            $source->type(),
            $source->getDirtyAttributes()
        );
    }

    public function getLike(int $user, $attributeCode, $attributeValue, $page = null): array
    {
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
