<?php

/**
 * @todo Add a isEqual() and isLike() function to compare attributables.
 */

declare(strict_types=1);

namespace Arete\Logos\Domain\Abstracts;

use Arete\Logos\Domain\Traits\ExposeAttributes;
use Arete\Common\Interfaces\Arrayable;
use Arete\Common\FillsProperties;
use Arete\Logos\Domain\Contracts\TypeRepository;
use Arete\Logos\Domain\Abstracts\Type;

abstract class Attributable implements Arrayable
{
    use ExposeAttributes;
    use FillsProperties;

    protected int $id;
    protected string $typeCode;
    protected ?Type $type = null;
    protected TypeRepository $types;
    protected ?string $genus = null;
    protected $ownerID = null;

    protected array $dirtyAttributes = [];

    public function __construct(
        TypeRepository $types,
        array $properties = []
    ) {
        $this->types = $types;
        $this->fill($properties);
    }

    public function __set($name, $value)
    {
        $this->dirtyAttributes[$name] = $value;
        return $this->attributes[$name] = $value;
    }

    public function getDirtyAttributes()
    {
        return $this->dirtyAttributes;
    }

    public function isDirty(): bool
    {
        return (bool) count($this->dirtyAttributes);
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

    public function ownerID()
    {
        return $this->ownerID;
    }
}
