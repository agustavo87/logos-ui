<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Defaults;

use Arete\Logos\Application\Interfaces\ValueTypeMapper;
use Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository as CreatorTypeRepositoryPort;
use Arete\Logos\Domain\Attribute;
use Arete\Logos\Domain\CreatorType;
use Arete\Logos\Domain\Schema;

/**
 * Uses the Logos schema as source of information about creator types
 * and maps its value types with the value type mapper.
 */
class CreatorTypeRepository implements CreatorTypeRepositoryPort
{
    protected Schema $schema;
    protected ValueTypeMapper $valueTypes;

    public function __construct(
        Schema $schema,
        ValueTypeMapper $valueTypes
    ) {
        $this->schema = $schema;
        $this->valueTypes = $valueTypes;
    }
    public function get($codeName): CreatorType
    {
        $creatorTypeData = $this->schema->creatorTypes()[$codeName];
        $creatorType = new CreatorType([
            'code_name' => $codeName,
            'label'     => $creatorTypeData['label'],
            'version'   => $this->schema::VERSION
        ]);
        $order = 0;
        foreach ($creatorTypeData['fields'] as list($fieldCodename, $fieldLabel)) {
            $creatorType->pushAttribute(
                $fieldCodename,
                new Attribute([
                    'code'  => $fieldCodename,
                    'type'  => $this->valueTypes->mapValueType($fieldCodename),
                    'label' => $fieldLabel,
                    'order' => $order++
                ])
            );
        }
        return $creatorType;
    }
}
