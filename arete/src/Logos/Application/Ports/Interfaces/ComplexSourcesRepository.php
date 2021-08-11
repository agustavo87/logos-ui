<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface ComplexSourcesRepository extends SourcesRepository
{
    /**
     * Filter sources by source properties ('key', 'id'), 'attributes'
     * or 'creator' attributes.
     *
     * @param array $params
     *
     * @return \Arete\Logos\Domain\Source[]
     */
    public function complexFilter(array $params): array;

    /**
     * Limit the ammount of results
     *
     * @param int $n
     *
     * @return self
     */
    public function limit(int $n): self;

    /**
     * Establish the offset to start retrieving results from
     *
     * @param int $i starts at 0
     *
     * @return self
     */
    public function offset(int $i): self;


    /**
     * @param string    $field
     * @param string    $group  wich group of fields of the source, could be:
     *                              - source : the main properties of the source
     *                              - attributes : an attribute of the source
     *                              - creator : an attribute of the most relevant creator
     * @todo in creator, order by the most relevant _primary_ creator.
     * @return self
     */
    public function orderBy(string $field, $group = 'source'): self;
}
