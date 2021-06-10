<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Logos\Ports\Interfaces\SourceRepository as SourceRepositoryPort;
use Arete\Logos\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Adapters\Laravel\Common\DBRepository;
use Arete\Logos\Adapters\Laravel\Common\DB;
use Arete\Logos\Models\Source;
use Arete\Logos\Models\ParticipationSet;

class DBSourceRepository extends DBRepository implements SourceRepositoryPort
{
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;

    public function __construct(
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        DB $db
    ) {
        parent::__construct($db);
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
    }

    public function createFromArray(array $params): Source
    {
        $type = $this->sourceTypes->get($params['type']);
        $source = new Source();
        $participations = new ParticipationSet($source);
        $sourceID =  $this->db->insertSource(
            $type->code(),
            1
        );
        foreach ($params['attributes'] as $code => $value) {
            $id = $this->db->insertAttribute(
                $sourceID,
                'source',
                $code,
                $value
            );
            if (!is_null($id)) {
                $source->pushAttribute($code, $value);
            }
        }
        $source->fill([
            'type' => $type,
            'participations' => $participations,
            'id' => $sourceID
        ]);
        return $source;
    }
}
