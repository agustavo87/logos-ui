<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository as SourcesRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;
use Arete\Logos\Application\Ports\Interfaces\ParticipationRepository;
use Arete\Logos\Domain\Contracts\Formatter;
use Arete\Logos\Domain\IndexResults;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;
use Arete\Logos\Domain\SourcesKeyGenerator;

class DBSourcesRepository extends DBRepository implements SourcesRepositoryPort, ComplexSourcesRepository
{
    use IndexResults;

    protected CreatorsRepository $creators;
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected ParticipationRepository $participations;
    protected Schema $schema;
    protected int $maxFetchSize = 30;
    protected array $cache = [];
    protected array $cacheByKey = [];
    protected Formatter $defaultFormatter;

    protected SourcesKeyGenerator $keyGenerator;

    public function __construct(
        CreatorsRepository $creators,
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        ParticipationRepository $participations,
        Formatter $defaultFormatter,
        Schema $schema,
        DB $db,
        LogosEnviroment $logos
    ) {
        parent::__construct($db, $logos);
        $this->creators = $creators;
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
        $this->participations = $participations;
        $this->defaultFormatter = $defaultFormatter;
        $this->schema = $schema;
        $this->keyGenerator = new SourcesKeyGenerator($this, $creators);
    }

    public function createFromArray(array $params, $ownerID = null): Source
    {
        $ownerID = $ownerID ?? $this->logos->getOwner();

        $key = $this->keyGenerator->getKey($params);

        /* Create the source and insert it's attributes */
        /** @todo Averiguar si hay que inertar un formatter */
        $source = new Source($this->sourceTypes, $this->defaultFormatter);
        $source->fill([
            'typeCode' => $params['type'],
            'ownerID' => $ownerID,
            'key'     => $key
        ]);

        /**
         * @todo    Quitar lógica de dominio de lógica de persistencia.
         *          Aquí para insertar los atributos DB tiene que "conocer"
         *          su estructura, esto acopla la DB al dominio, cuando
         *          sólo debería estar acoplada a la persistencia.
         * */
        $this->db->insertEntityAttributes(
            $source,
            $params['attributes']
        );

        /* Add the participations */
        $participations = new ParticipationSet($source, $this->creators, $this->participations);

        if (array_key_exists('participations', $params)) {
            foreach ($params['participations'] as $participationData) {
                $participations->pushNew(
                    $participationData['creator'],
                    $participationData['role'],
                    $participationData['relevance']
                );
            }
        }
        $source->simpleFill([
            'participations' => $participations
        ]);

        /* Cache it and return it */
        $this->cache[$source->id()] = $source;
        $this->cacheBykey[$source->key()] = $source;
        return $source;
    }

    public function keyExist($key): bool
    {
        return $this->db->sourceKeyExist($key);
    }

    public function getByKey(string $key)
    {
        if (array_key_exists($key, $this->cacheByKey)) {
            return $this->cacheByKey[$key];
        }
        return $this->getNewByKey($key);
    }

    /** @todo agregarlo a la interfáz */
    public function getNewByKey($key)
    {
        return $this->getNew($this->db->getSourceIDByKey($key));
    }

    public function get(int $id): Source
    {
        if (array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }
        return $this->getNew($id);
    }

    /**
     * Fetch new source from persistence even if it's already feteched
     *
     * This can create parallel version of same entity and have unpredicted results.
     *
     * @param int $id
     *
     * @return Source
     */
    public function getNew(int $id): Source
    {
        /** @todo Esto es lógica de persistencia, no debería estar aquí. */
        $ownerFKColumn = $this->logos->getOwnersTableData()->FK;

        /* Create the source with it's attributes */
        $attributes = $this->db->getEntityAttributes($id);
        $sourceEntry = $attributes->first();
        $source = new Source(
            $this->sourceTypes,
            $this->defaultFormatter,
            [
                'id' => $sourceEntry->id,
                'key' => $sourceEntry->key,
                'typeCode' => $sourceEntry->source_type_code_name,
                'ownerID' => $sourceEntry->$ownerFKColumn
            ]
        );
        // hidrate the model attributes
        foreach ($attributes as $code => $data) {
            $source->pushAttribute(
                $code,
                $data->value
            );
        }

        /* Add participations in it's creation. */
        $participations = new ParticipationSet($source, $this->creators, $this->participations);
        $source->simpleFill([
            'participations' => $participations->load()
        ]);
        $this->cache[$id] = $source;
        $this->cacheBykey[$source->key()] = $source;
        return $source;
    }

    public function save(Source $source): bool
    {
        $source->participations()->save();

        return (bool) $this->db->insertAttributes(
            $source->id(),
            $source->type(),
            $source->getDirtyAttributes()
        );
    }

    public function getLike($attributeCode, $attributeValue, $ownerID = null, $page = null): array
    {
        $user = $user ?? $this->logos->getOwner();
        $entitiesIDs = $this->db->findEntitiesWith(
            'source',
            $attributeCode,
            $attributeValue,
            $ownerID
        );

        $result = [];
        $take = count($entitiesIDs) > $this->maxFetchSize ? $this->maxFetchSize : count($entitiesIDs);
        for ($i = 0; $i <= $take - 1; $i++) {
            $creator = $this->get($entitiesIDs[$i]);
            $result[] = $creator;
        }

        return $result;
    }

    public function complexFilter(array $params): array
    {
        $selectedIDs = $this->db->getSourceIDsWith(
            $params,
            [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'orderBy' => [
                    'group' => $this->orderBy['group'],
                    'field' => $this->orderBy['field']
                ]
            ]
        );
        $sources = [];
        foreach ($selectedIDs as $sourceID) {
            $sources[] = $this->get($sourceID);
        }
        return array_values($sources);
    }

    public function flush()
    {
        $this->cache = [];
    }
}
