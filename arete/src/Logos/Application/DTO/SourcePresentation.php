<?php

declare(strict_types=1);

namespace Arete\Logos\Application\DTO;

use Arete\Common\Interfaces\Arrayable;
use JsonSerializable;

class SourcePresentation implements JsonSerializable, Arrayable
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
        $this->attributes = $attributes;
    }



    public function __toString()
    {
        return $this->label;
    }

    public function toArray()
    {
        return [
            'code' => $this->code,
            'value' => $this->value
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
