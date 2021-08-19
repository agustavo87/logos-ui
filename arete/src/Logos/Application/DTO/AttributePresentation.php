<?php

declare(strict_types=1);

namespace Arete\Logos\Application\DTO;

class AttributePresentation
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
}
