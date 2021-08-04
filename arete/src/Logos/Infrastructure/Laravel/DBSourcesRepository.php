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
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;

use function Arete\Common\simplifyWord;

class DBSourcesRepository extends DBRepository implements SourcesRepositoryPort, ComplexSourcesRepository
{
    protected CreatorsRepository $creators;
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected ParticipationRepository $participations;
    protected Schema $schema;
    protected int $maxFetchSize = 30;
    protected array $cache = [];
    protected array $cacheByKey = [];
    protected Formatter $defaultFormatter;
    public static array $diferenciators = [
        'a','b','c','d','e','f','g','h','i','j','k','m','n','o','p',
        'q', 'r', 's', 't', 'u', 'b', 'w', 'x', 'y', 'z'
    ];

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
    }

    public function createFromArray(array $params, $ownerID = null): Source
    {
        $ownerID = $ownerID ?? $this->logos->getOwner();

        // first, let's create the source and insert it's attributes
        $key = $this->getKey($params);
        $source = new Source($this->sourceTypes, $this->defaultFormatter);
        $source->fill([
            'typeCode' => $params['type'],
            'ownerID' => $ownerID,
            'key'     => $key
        ]);

        $this->db->insertEntityAttributes(
            $source,
            $params['attributes']
        );

        // then add the participations
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

        $this->cache[$source->id()] = $source;
        $this->cacheBykey[$source->key()] = $source;
        return $source;
    }

    public function getKey(array $params): string
    {
        if (isset($params['key'])) {
            $keyWord = $params['key'];
        } else {
            $keyWord = $this->generateKeyWord($params);
        }
        $i = 1;
        $baseKeyWord = $keyWord;
        while ($this->keyExist($keyWord)) {
            $keyWord = $baseKeyWord . $this->getDiferenciator(++$i);
        }
        return $keyWord;
    }

    protected function generateKeyWord(array $params): string
    {
        $keyWord = $this->getCreatorKeyWord($params);

        if ($keyWord == '') {
            if (isset($params['title'])) {
                $keyWord = explode(' ', $params['title'])[0];
            } else {
                $keyWord = 'anon';
            }
        }

        $keyWord = simplifyWord($keyWord);
        if (isset($params['attributes']['date'])) {
            $keyWord .= $params['attributes']['date']->format('Y');
        }

        return $keyWord;
    }

    protected function getCreatorKeyWord(array $params): string
    {
        if (!isset($params['participations'])) {
            return '';
        }

        // look for valid relevant participation
        $authors = array_filter(
            $params['participations'],
            /** @todo seleccionar creador primario */
            fn ($part) => $part['role'] == 'author'
        );
        $authors = array_values($authors);
        if (count($authors)) {
            /** @todo seleccionar el más relevante */
            $participation = $authors[0];
        } else {
            $participation = $params['participations'][0];
        }

        $creator = [];
        if (isset($participation['creator']['creatorID'])) {
            $creator = $this->creators->get(
                $participation['creator']['creatorID']
            )->toArray();
        } else {
            $creator = $participation['creator'];
        }

        // get some relevant attribute
        if ($creator['type'] == 'person') {
            return $creator['attributes']['lastName'];
        }

        return array_values($creator['attributes'])[0];
    }

    protected function getDiferenciator(int $i): string
    {
        if ($i < count(self::$diferenciators)) {
            return self::$diferenciators[$i - 1];
        }
        return '-' . $i;
    }

    public function keyExist($key)
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

    /** @todo agregarlo a la interfas */
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
        $ownerFKColumn = $this->logos->getOwnersTableData()->FK;

        // lets create the source with it's attributes
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

        // lets add participations in it's creation.
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
        $selectedIDs = $this->db->getSourceIDsWith($params);
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
