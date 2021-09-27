<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface CreateSourceUC
{
    /**
     * Return presentation data of source types
     *
     * @return \Arete\Logos\Application\DTO\SourceTypePresentation[]
     */
    public function presentSourceTypes(): array;

    /**
     * @param $ownerID
     * @param string $type
     * @param array $attributes
     * @param array $creators
     * @param string|null $key
     *
     * @return string key
     */
    public function create($ownerID, string $type, array $attributes, array $creators, ?string $key = null): string;

    /**
     * Suggest a valid (unique) key.
     *
     * @param array|string $params  A simple string to check or a set of
     *                              key => value parameters array for example
     *                              ['ownerID' => 2, 'key' => 'beck2020']
     *
     * @return string
     */

    public function sugestKey($params): string;

    public function suggestCreators(
        $owner,
        string $hint,
        string $attribute = 'lastName',
        string $type = 'person',
        string $orderBy = 'lastName',
        bool $asc = true,
        int $limit = 5
    ): array;
}
