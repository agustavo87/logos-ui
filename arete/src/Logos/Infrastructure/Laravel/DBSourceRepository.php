<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\SourceRepository as SourceRepositoryPort;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Domain\Source;
use Arete\Logos\Domain\Schema;
use Arete\Logos\Domain\ParticipationSet;

class DBSourceRepository extends DBRepository implements SourceRepositoryPort
{
    protected SourceTypeRepository $sourceTypes;
    protected CreatorTypeRepository $creatorTypes;
    protected Schema $schema;

    public function __construct(
        SourceTypeRepository $sourceTypes,
        CreatorTypeRepository $creatorTypes,
        Schema $schema,
        DB $db
    ) {
        parent::__construct($db);
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
        $this->schema = $schema;
    }

    public function createFromArray(array $params): Source
    {
        $source = new Source($this->sourceTypes);
        $participations = new ParticipationSet($source);
        $source->fill([
            'typeCode' => $params['type'],
            'participations' => $participations,
        ]);
        $this->db->insertEntityAttributes(
            $source,
            $params['attributes'],
            1
        );
        return $source;
    }

    public function get(int $id)
    {
        $attributes = $this->db->getEntityAttributes($id);
        $sourceEntry = $attributes->first();
        $source = new Source(
            $this->sourceTypes,
            [
                'id' => $sourceEntry->id,
                'typeCode' => $sourceEntry->source_type_code_name
            ]
        );
        $participations = new ParticipationSet($source);
        $source->fill([
            'participations' => $participations
        ]);

        foreach ($attributes as $code => $data) {
            $source->pushAttribute(
                $code,
                $data->value
            );
        }
        return $source;
    }
}
