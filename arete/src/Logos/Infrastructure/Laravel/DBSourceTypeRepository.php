<?php

/**
 * @todo this could be cached, becouse it remains the same most of the time.
 */

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\DTO\AttributePresentation;
use Arete\Logos\Application\DTO\SourcePresentation;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;
use Arete\Logos\Application\Ports\Interfaces\SourcesTranslator;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Domain\Abstracts\SourceType;
use Arete\Logos\Infrastructure\Laravel\Models\LvSourceType;
use Arete\Logos\Infrastructure\Laravel\Common\DBRepository;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use Arete\Logos\Application\Ports\Interfaces\LogosEnviroment;

/**
 * Laravel dependent Data Base based Source Type Repository
 */
class DBSourceTypeRepository extends DBRepository implements SourceTypeRepository, CreateSourceUC
{
    protected SourcesTranslator $sourcesTranslator;

    public function __construct(
        DB $db,
        LogosEnviroment $logos,
        SourcesTranslator $sourcesTranslator
    ) {
        $this->sourcesTranslator = $sourcesTranslator;
        parent::__construct($db, $logos);
    }
    public function get($codeName): SourceType
    {
        $schema = $this->db->getSourceSchema($codeName);
        return LvSourceType::fromLvData(
            $this->db->getSourceType($codeName),
            $schema,
            $this->db->getSchemaAttributes($schema->id),
            $this->db->getRoles($codeName)
        );
    }

    public function types(): array
    {
        return $this->db
                    ->getSourceTypeNames()
                    ->map(fn ($obj) => $obj->code_name)
                    ->toArray();
    }

    public function attributes(?string $type = null): array
    {
        return $this->db->getSourceTypeAttributes($type)
                 ->map(fn ($obj) => $obj->attribute_type_code_name)
                 ->toArray();
    }

    public function presentSourceTypes(): array
    {
        $types = $this->db->getSourceTypeData(['code_name']);
        $data = [];
        foreach ($types as $type) {
            $label = $this->sourcesTranslator->translate($type->code_name, 'types');
            $attributes = $this->getAttributePresentations($type->code_name);
            $data[$type->code_name] = new SourcePresentation($type->code_name, $label, $attributes);
        }

        return $data;
    }

    /**
     * @param string[] $attrCodes
     *
     * @return \Arete\Logos\Application\DTO\AttributePresentation[]
     */
    public function getAttributePresentations(string $typeCode): array
    {
        $attributesData = $this->db->getSourceTypeAttributes(
            $typeCode,
            null,
            true,
            ['*']
        );
        $presentations = [];
        foreach ($attributesData as $attributeData) {
            $presentations[] = new AttributePresentation(
                $attributeData->attribute_type_code_name,
                $attributeData->base_attribute_type_code_name,
                $attributeData->label,
                $attributeData->value_type,
                (int) $attributeData->order
            );
        }
        return $presentations;
    }
}
