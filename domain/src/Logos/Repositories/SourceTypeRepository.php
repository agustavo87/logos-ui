<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories;

use Arete\Logos\Models\SourceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SourceTypeRepository implements SourceTypeRepositoryInterface
{

    public function get($codeName): SourceType
    {
        $sourceType = DB::table('source_types')->where([
            'code_name' => $codeName,
        ])->first();
        Log::info("Source Type '{$codeName}'", ['data' => $sourceType]);

        $schema = DB::table('schemas')
                    ->where('type_code_name', $codeName)
                    ->where('type', config('logos.schemaTypes.source'))
                    ->latest()
                    ->first();
        Log::info("Source Type Schema '{$codeName}'", ['data' => $schema]);

        $attributes = DB::table('schema_attributes')
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
                        ->where('schema_attributes.schema_id', $schema->id)
                        ->get();
        Log::info("SourceType Attributes '{$codeName}'", ['data' => $attributes]);

        $roles = DB::table('participation_types')
                    ->join('roles', 'participation_types.role_code_name', '=', 'roles.code_name')
                    ->select('roles.*')
                    ->where('participation_types.source_type_code_name', $codeName)
                    ->get();
        Log::info("Roles of '{$codeName}'", ['data' => $roles]);

        return new SourceType($sourceType, $schema, $attributes, $roles);
    }
}
