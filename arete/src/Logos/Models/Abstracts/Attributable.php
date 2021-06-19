<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Abstracts;

use Arete\Logos\Models\Traits\ExposeAttributes;
use Arete\Common\Interfaces\Arrayable;
use Arete\Common\FillsProperties;
use Arete\Logos\Interfaces\TypeRepository;
use Arete\Logos\Models\Abstracts\Type;

class Attributable implements Arrayable
{
    use ExposeAttributes;
    use FillsProperties;

    protected int $id;
    protected string $typeCode;
    protected ?Type $type = null;
    protected TypeRepository $types;
    protected ?string $genus = null;

    public function __construct(
        TypeRepository $types,
        array $properties = []
    ) {
        $this->types = $types;
        $this->fill($properties);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function type(): Type
    {
        return $this->type ?? ($this->type = $this->types->get($this->typeCode));
    }

    public function typeCode(): string
    {
        return $this->typeCode;
    }

    public function genus(): ?string
    {
        return $this->genus;
    }
}
