<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Creator;

class DBCreatorsRepository extends DBRepository implements CreatorsRepository
{
    protected CreatorTypeRepository $creatorTypes;
    protected int $maxFetchSize = 30;

    public function __construct(
        DB $db,
        LogosEnviroment $logos,
        CreatorTypeRepository $creatorTypes
    ) {
        parent::__construct($db, $logos);
        $this->creatorTypes = $creatorTypes;
    }

    public function createFromArray(array $params, $userId = 1): Creator
    {
        $creator = new Creator(
            $this->creatorTypes,
            [
                'typeCode'  => $params['type']
            ]
        );

        $this->db->insertEntityAttributes(
            $creator,
            $params['attributes'],
            1
        );

        return $creator;
    }

    public function get(int $id): ?Creator
    {
        $attributes = $this->db->getEntityAttributes($id, 'creator');
        $creatorEntry = $attributes->first();
        $creator = new Creator(
            $this->creatorTypes,
            [
                'id'        => $creatorEntry->id,
                'typeCode'  => $creatorEntry->creator_type_code_name
            ]
        );
        foreach ($attributes as $code => $data) {
            $creator->pushAttribute(
                $code,
                $data->value
            );
        }
        return $creator;
    }

    public function save(Creator $creator): bool
    {
        return (bool) $this->db->insertAttributes(
            $creator->id(),
            $creator->type(),
            $creator->getDirtyAttributes()
        );
    }

    public function getLike(int $user, $attributeCode, $attributeValue, $page = null): array
    {
        $entitiesIDs = $this->db->findEntitiesWith('creator', $attributeCode, $attributeValue);

        $result = [];
        $take = count($entitiesIDs) > $this->maxFetchSize ? $this->maxFetchSize : count($entitiesIDs);
        for ($i = 0; $i <= $take - 1; $i++) {
            $creator = $this->get($entitiesIDs[$i]);
            $result[] = $creator;
        }

        return $result;
    }
}
