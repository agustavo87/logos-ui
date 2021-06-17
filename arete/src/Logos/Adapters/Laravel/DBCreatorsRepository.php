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

        $attributeTypes = $this->db->getAttributeTypes(array_keys($params['attributes']));
        foreach ($params['attributes'] as $code => $value) {
            $id = $this->db->insertAttribute(
                $creatorID,
                'creator',
                $code,
                $value,
                $attributeTypes[$code]->value_type
            );
            if (!is_null($id)) {
                $creator->pushAttribute($code, $value);
            }
        }

        return $creator;
    }

    public function get(int $id): ?Creator
    {
        $creatorEntry = $this->db->getCreator($id);
        $type = $this->creatorTypes->get($creatorEntry->creator_type_code_name);
        $creator = new Creator(
            $this->creatorTypes,
            [
                'typeCode'  => $type->code(),
                'id'        => $creatorEntry->id
                ]
            );
        /** @todo realizar un join en la obtención de los atributos con su value_type */
        $attributes = $this->db->getContcreteAttributes($creatorEntry->id, 'creator');
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

    // public function get(int $id)
    // {
    //     $source = $this->db->getSource($id);
    //     $type = $this->sourceTypes->get($source->source_type_code_name);
    //     $source = new Source([
    //         'id' => $source->id,
    //         'type' => $type
    //     ]);
    //     $participations = new ParticipationSet($source);
    //     $source->fill([
    //         'participations' => $participations
    //     ]);

    //     $attributes = $this->db->getSourceAttributes($id);
    //     foreach ($attributes as $code => $data) {
    //         $source->pushAttribute(
    //             $code,
    //             $this->resolveAttributeValue($type, (array) $data)
    //         );
    //     }
    //     return $source;
    // }
    // /**
    //  * Resolves the value from its type
    //  *
    //  * @param SourceType $type
    //  * @param array $attributeData
    //  *
    //  * @return mixed
    //  */
    // public function resolveAttributeValue(SourceType $type, array $attributeData)
    // {
    //     $attribute = $type->{$attributeData['attribute_type_code_name']};
    //     $valueType = $attribute->type;
    //     $valueColumn = $this->db::VALUE_COLUMS[$valueType];
    //     return $attributeData[$valueColumn];
    // }
}
