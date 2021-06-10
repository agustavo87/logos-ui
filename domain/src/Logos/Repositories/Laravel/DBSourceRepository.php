<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories\Laravel;

use Arete\Logos\Repositories\Conceptual\SourceRepository;
use Arete\Logos\Models\SourceInterface;
use Arete\Logos\Models\Source;
use Arete\Logos\Models\ParticipationSet;
use Arete\Logos\Repositories\CreatorTypeRepositoryInterface;
use Arete\Logos\Repositories\SourceTypeRepositoryInterface;
use Arete\Logos\Services\Laravel\DB;

class DBSourceRepository extends SourceRepository
{
    protected DB $db;

    public function __construct(
        SourceTypeRepositoryInterface $sourceTypes,
        CreatorTypeRepositoryInterface $creatorTypes,
        DB $db
    ) {
        parent::__construct($sourceTypes, $creatorTypes);
        $this->db = $db;
    }

    protected function FromArray(array $params): SourceInterface
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
