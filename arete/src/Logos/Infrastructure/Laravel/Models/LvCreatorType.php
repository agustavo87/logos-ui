<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel\Models;

use stdClass;
use Illuminate\Support\Collection;
use Arete\Logos\Models\{CreatorType, Attribute};

/**
 * Laravel dependent CreatorType
 */
class LvCreatorType extends CreatorType
{
    /**
     * Constructs from laravel data types
     *
     * @param stdClass $creatorType
     * @param stdClass $schema
     * @param Collection $attributes
     *
     * @return CreatorType
     */
    public static function fromLvData(
        stdClass $creatorType,
        stdClass $schema,
        Collection $attributes
    ): CreatorType {
        $instance = new static();
        $instance->code_name = $creatorType->code_name;
        $instance->label = $creatorType->label;
        $instance->version = $schema->version;
        foreach ($attributes as $attribute) {
            $instance->attributes[$attribute->code_name] = new Attribute([
                'type'  => $attribute->value_type,
                'code'  => $attribute->code_name,
                'base'  => $attribute->base_attribute_type_code_name,
                'label' => $attribute->label,
                'order' => (int) $attribute->order
            ]);
        }
        return $instance;
    }
}
