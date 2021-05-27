<?php

declare(strict_types=1);

namespace Arete\Logos\Aggregates\Source;

class Creator
{
    public string $type;
    public string $schema;
    public string $role;
    public int $relevance;
    public int $id;
    protected Attributes $attributes;

    /**
     * return the attributes
     *
     * @param Attributes|null $attributes
     *
     * @return [type]
     */
    public function attr(?Attributes $attributes = null)
    {
        if ($attributes) {
            return $this->attributes = $attributes;
        }
        return $this->attributes;
    }
}
