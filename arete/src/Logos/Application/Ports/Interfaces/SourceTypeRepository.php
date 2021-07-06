<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Contracts\TypeRepository;
use Arete\Logos\Domain\Abstracts\SourceType;

interface SourceTypeRepository extends TypeRepository
{
    public function get($codeName): SourceType;

    /**
     * Get available types
     *
     * @return array
     */
    public function types(): array;

    /**
     * Get availables attributes
     *
     * @param string|null $type specify the source type wich the attribute
     *                          belongs. if `null` all attributes are returned.
     *
     * @return array
     */
    public function attributes(?string $type = null): array;
}
