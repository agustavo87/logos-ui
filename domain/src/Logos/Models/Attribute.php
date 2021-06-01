<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

class Attribute
{
    public string $code;
    public ?string $base;
    public ?string $label;
    public string $type;
    public int $order;

    public function __construct($attribute)
    {
        $this->type = $attribute->value_type;
        $this->code = $attribute->code_name;
        $this->base = $attribute->base_attribute_type_code_name;
        $this->label = $attribute->label;
        $this->order = (int) $attribute->order;
    }
}
