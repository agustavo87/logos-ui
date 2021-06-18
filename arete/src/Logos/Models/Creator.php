<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Common\FillsProperties;
use Arete\Common\Interfaces\Arrayable;
use Arete\Logos\Models\Traits\ExposeAttributes;
use Arete\Logos\Ports\Interfaces\CreatorTypeRepository;

class Creator implements Arrayable
{
    use ExposeAttributes;
    use FillsProperties;

    protected int $id;

    /**
     * @var string creator type code name
     */
    protected string $typeCode;

    protected ?CreatorType $type = null;

    protected CreatorTypeRepository $creatorTypes;

    public function __construct(
        CreatorTypeRepository $creatorTypes,
        array $properties = [],
        array $attributes = []
    ) {
        $this->creatorTypes = $creatorTypes;
        $this->fill($properties);
        $this->attributes = $attributes;
    }

    public function id(): int
    {
        return $this->id;
    }

    /**
     * The creator type code name
     *
     * @return string
     */
    public function typeCode(): string
    {
        return $this->typeCode;
    }

    public function type(): CreatorType
    {
        return $this->type ?? ($this->type = $this->creatorTypes->get($this->typeCode));
    }
}
