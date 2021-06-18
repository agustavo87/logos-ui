<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Adapters\Laravel\Common\DBRepository;
use Arete\Logos\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Adapters\Laravel\Common\DB;
use Arete\Logos\Models\Creator;
use Arete\Logos\Models\CreatorType;

class DBCreatorsRepository extends DBRepository implements CreatorsRepository
{
    protected CreatorTypeRepository $creatorTypes;

    public function __construct(
        DB $db,
        CreatorTypeRepository $creatorTypes
    ) {
        parent::__construct($db);
        $this->creatorTypes = $creatorTypes;
    }

    public function createFromArray(array $params, $userId = 1): Creator
    {
        $creatorID =  $this->db->insertCreator(
            $params['type'],
            $userId
        );

        $creator = new Creator(
            $this->creatorTypes,
            [
                'id'        => $creatorID,
                'typeCode'  => $params['type']
            ]
        );

        $this->db->insertEntityAttributes(
            $creator,
            'creator',
            $params['attributes']
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
        return true;
    }

    public function getLike(int $user, array $criteria): array
    {
        return [];
    }
}
