<?php

declare(strict_types=1);

namespace Arete\Logos\Application\DTO;

use Arete\Common\Interfaces\Arrayable;
use JsonSerializable;
use Stringable;

/**
 * [Description AttributePresentation]
 */
class RolePresentation implements Arrayable, JsonSerializable, Stringable
{
    public string $code;
    public ?string $label;
    public bool $primary;

    public function __construct(
        string $code,
        ?string $label,
        bool $primary
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->primary = $primary;
    }

    public function toArray()
    {
        return [
            'code' => $this->code,
            'label' => $this->label,
            'primary'  => $this->primary,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return $this->label;
    }
}
