<?php

declare(strict_types=1);

namespace Arete\Logos\Application\DTO;

use Arete\Common\Interfaces\Arrayable;
use JsonSerializable;
use Stringable;

class AttributePresentation implements Arrayable, JsonSerializable, Stringable
{
    public string $code;
    public ?string $baseAttributeCode;
    public ?string $label;
    public string $type;
    public int $order;

    public function __construct(
        string $code,
        ?string $baseAttributeCode,
        ?string $label,
        string $type,
        int $order
    ) {
        $this->code = $code;
        $this->baseAttributeCode = $baseAttributeCode;
        $this->label = $label;
        $this->type = $type;
        $this->order = $order;
    }

    public function toArray()
    {
        return [
            'code' => $this->code,
            'base'  => $this->baseAttributeCode,
            'label' => $this->label,
            'type'  => $this->type,
            'order' => $this->order
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}
