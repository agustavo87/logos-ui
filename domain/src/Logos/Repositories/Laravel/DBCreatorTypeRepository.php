<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories\Laravel;

use Arete\Logos\Repositories\CreatorTypeRepositoryInterface;
use Arete\Logos\Models\CreatorType;
use Arete\Logos\Models\Laravel\LvCreatorType;
use Arete\Logos\Models\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Laravel dependent Data Base based Source Type Repository
 */
class DBCreatorTypeRepository implements CreatorTypeRepositoryInterface
{

    public function get($codeName): CreatorType
    {
        $schema = $this->getSchema($codeName);
        return LvCreatorType::fromLvData(
            $this->getCreatorType($codeName),
            $schema,
            $this->getAttributes($schema->id),
        );
    }

    protected function getSchema($codeName)
    {
        return DB::table('schemas')
            ->where('type_code_name', $codeName)
            ->where('type', Schema::TYPES['creator'])
            ->latest()
            ->first();
    }


    protected function getCreatorType($codeName)
    {
        return DB::table('creator_types')->where([
            'code_name' => $codeName,
        ])->first();
    }

    protected function getAttributes($schemaId)
    {
        return DB::table('schema_attributes')
            ->join(
                'attribute_types',
                'schema_attributes.attribute_type_code_name',
                '=',
                'attribute_types.code_name'
            )
            ->select(
                'attribute_types.code_name',
                'attribute_types.base_attribute_type_code_name',
                'attribute_types.value_type',
                'schema_attributes.label',
                'schema_attributes.order',
            )
            ->where('schema_attributes.schema_id', $schemaId)
            ->get();
    }
}
