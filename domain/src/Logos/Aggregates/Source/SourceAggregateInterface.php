<?php

declare(strict_types=1);

namespace Arete\Logos\Aggregates\Source;

interface SourceAggregateInterface
{
    public $type;
    public $schema;

    public function id(): int;

    /**
     * Get/Set attributes
     * 
     * If $creators is null get creators. Set Otherwise
     * @param null $attributes
     * 
     * @return array
     */
    public function attr(Attributes $attributes): Attributes;
    
    /**
     * Get creators
     * 
     * If $creators is null get creators. Set Otherwise
     * @param null $creators
     * 
     * @return array
     */
    public function creators(): array;

    /**
     * Attach a creator
     * 
     * @param Creator $creator
     * 
     * @return [type]
     */
    public function attachCreator(Creator $creator, $role, $order);
}
