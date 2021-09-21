<?php

declare(strict_types=1);

namespace Arete\Logos\Application\DTO;

use Arete\Common\Interfaces\Arrayable;
use JsonSerializable;
use Stringable;

class SourceTypePresentation implements JsonSerializable, Arrayable, Stringable
{
    public string $code;
    public string $label;

    /**
     * @var \Arete\Logos\Application\DTO\AttributePresentation[]
     */
    public array $attributes;

    /**
     * @var \Arete\Logos\Application\DTO\RolePresentation[]
     */
    public array $roles;

    /**
     * @param string $code
     * @param string $label
     * @param \Arete\Logos\Application\DTO\AttributePresentation[] $attributes
     * @param \Arete\Logos\Application\DTO\RolePresentation[] $roles
     */
    public function __construct(string $code, string $label, array $attributes = [], array $roles = [])
    {
        $this->code = $code;
        $this->label = $label;
        if (count($attributes)) {
            usort($attributes, fn ($a, $b) => $a->order <=> $b->order);
        }
        $this->attributes = $attributes;
        $this->roles = $roles;
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
            'attributes' => [],
            'roles' => []
        ];
        foreach ($this->attributes as $attr) {
            $data['attributes'][$attr->order] = $attr->toArray();
        }
        foreach ($this->roles as $roles) {
            $data['roles'][$roles->code] = $roles->toArray();
        }
        return $data;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
