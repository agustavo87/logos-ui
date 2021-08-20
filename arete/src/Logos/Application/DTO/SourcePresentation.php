<?php

declare(strict_types=1);

namespace Arete\Logos\Application\DTO;

use Arete\Common\Interfaces\Arrayable;
use JsonSerializable;
use Stringable;

class SourcePresentation implements JsonSerializable, Arrayable, Stringable
{
    public string $code;
    public string $label;

    /**
     * @var \Arete\Logos\Application\DTO\AttributePresentation[]
     */
    public array $attributes;

    public function __construct($code, $label, array $attributes = [])
    {
        $this->code = $code;
        $this->label = $label;
        if (count($attributes)) {
            usort($attributes, fn ($a, $b) => $a->order <=> $b->order);
        }
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    public function toArray()
    {
        $data =  [
            'code' => $this->code,
            'label' => $this->label,
            'attributes' => []
        ];
        foreach ($this->attributes as $attr) {
            $data['attributes'][$attr->order] = $attr->toArray();
        }
        return $data;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
