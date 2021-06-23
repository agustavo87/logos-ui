<?php

/**
 * @todo a todos los metodos agregar la opción de especificar el usuario
 * o utilizar el usuario por defecto.
 */

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository as SourcesRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
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
    protected static string $defaultOwner = '1';
    protected int $maxFetchSize = 30;

    public function __construct(
        CreatorsRepository $creators,
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        ParticipationRepository $participations,
        Schema $schema,
        DB $db
    ) {
        parent::__construct($db);
        $this->creators = $creators;
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
        $this->participations = $participations;
        $this->schema = $schema;
    }

    public function createFromArray(array $params, ?string $ownerID = null): Source
    {
        $ownerID = $ownerID ?? self::$defaultOwner;

        // first, let's create the source and insert it's attributes
        $source = new Source($this->sourceTypes);
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
        // lets create the source with it's attributes
        $attributes = $this->db->getEntityAttributes($id);
        $sourceEntry = $attributes->first();
        $source = new Source(
            $this->sourceTypes,
            [
                'id' => $sourceEntry->id,
                'typeCode' => $sourceEntry->source_type_code_name
            ]
        );
        foreach ($attributes as $code => $data) {
            $source->pushAttribute(
                $code,
                $data->value
            );
        }

        // lets add participations in it's creation.
        $participations = new ParticipationSet($source, $this->creators, $this->participations);
        $source->fill([
            'participations' => $participations
        ]);
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

    public static function setOwner(string $id)
    {
        self::$defaultOwner = $id;
    }
}
