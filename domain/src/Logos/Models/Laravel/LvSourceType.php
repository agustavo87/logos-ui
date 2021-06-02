<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Laravel;

use stdClass;
use Illuminate\Support\Collection;
use Arete\Logos\Models\{SourceType, Attribute};

/**
 * Laravel dependent SourceType
 */
class LvSourceType extends SourceType
{
    /**
     * Constructs from laravel data types
     *
     * @param stdClass $sourceType
     * @param stdClass $schema
     * @param Collection $attributes
     * @param Collection $roles
     *
     * @return SourceType
     */
    public static function fromLvData(
        stdClass $sourceType,
        stdClass $schema,
        Collection $attributes,
        Collection $roles
    ): SourceType {
        $instance = new static();
        $instance->code_name = $sourceType->code_name;
        $instance->label = $sourceType->label;
        $instance->version = $schema->version;
        foreach ($attributes as $attribute) {
            $instance->attributes[$attribute->code_name] = new Attribute(get_object_vars($attribute));
        }
        $instance->roles = LvRoleCollection::fromLvData($roles, $instance);
        return $instance;
    }
}
