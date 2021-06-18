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
        $creatorEntry = $this->db->getCreator($id);
        $type = $this->creatorTypes->get($creatorEntry->creator_type_code_name);
        $creator = new Creator(
            $this->creatorTypes,
            [
                'type'      => $type,
                'typeCode'  => $type->code(),
                'id'        => $creatorEntry->id
            ]
        );
        /** @todo realizar un join en la obtención de los atributos con su value_type */
        $attributes = $this->db->getEntityAttributes($creatorEntry->id, 'creator');
        foreach ($attributes as $code => $data) {
            $creator->pushAttribute(
                $code,
                $this->resolveAttributeValue($type, (array) $data)
            );
        }
        return $creator;
    }

    public function resolveAttributeValue(CreatorType $type, array $attributeData)
    {
        $attribute = $type->{$attributeData['attribute_type_code_name']};
        $valueType = $attribute->type;
        $valueColumn = $this->db::VALUE_COLUMS[$valueType];
        return $attributeData[$valueColumn];
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
