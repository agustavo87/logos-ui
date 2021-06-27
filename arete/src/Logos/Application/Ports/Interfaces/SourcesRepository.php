<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

use Arete\Logos\Domain\Source;

interface SourcesRepository
{
    public function createFromArray(array $params): Source;

    public function get(int $id): Source;

    /**
     * Fetch new source from persistence even if it's already feteched
     *
     * This can create parallel version of same entity and have unpredicted results.
     *
     * @param int $id
     *
     * @return Source
     */
    public function getNew(int $id): Source;

    public function save(Source $source): bool;


    /**
     *  Give the sources who matches the specified criteria
     *
     * @param string $attributeCode
     * @param string $attributeValue
     * @param int|null $user
     *
     * @return Source[]
     */
    public function getLike(string $attributeCode, string $attributeValue, ?int $user = null): array;
}
