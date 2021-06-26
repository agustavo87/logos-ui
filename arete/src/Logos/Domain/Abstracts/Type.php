<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Abstracts;

use Arete\Logos\Domain\Traits\ExposeAttributes;

/**
 * For entity types
 *
 * count(): N attributes
 */
abstract class Type implements \Countable
{
    use ExposeAttributes;

    protected ?string $genus = '';
    protected string $code_name;
    protected ?string $label = null;
    protected string $version;

    public function code(): string
    {
        return $this->code_name;
    }

    public function __toString()
    {
        return $this->code();
    }

    public function label(): ?string
    {
        return $this->label;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function names(): array
    {
        return $this->attributes();
    }

    public function genus(): ?string
    {
        return $this->genus;
    }
}
