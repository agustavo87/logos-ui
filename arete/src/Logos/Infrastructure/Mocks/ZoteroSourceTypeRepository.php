<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Mocks;

use Arete\Logos\Application\ValueTypeMapper;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository as SourceTypeRepository;
use Arete\Logos\Application\SourceTypeLabelsMap;
use Arete\Logos\Domain\SourceType;
use Arete\Logos\Domain\Zotero\ZoteroSchema;
use Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface as ZoteroSchemaLoader;
use Arete\Logos\Domain\Attribute;
use Arete\Logos\Domain\Role;
use Arete\Logos\Domain\RoleCollection;

class ZoteroSourceTypeRepository implements SourceTypeRepository
{
    /**
     * @var SourceType[]
     */
    public array $sourceTypes = [];

    public static array $availableZoteroItemsTypes = ['journalArticle', 'book'];

    public ValueTypeMapper $valueTypes;
    public SourceTypeLabelsMap $sourceTypeLabels;
    public ZoteroSchema $zoteroSchema;

    public function __construct(
        ValueTypeMapper $valueTypes,
        SourceTypeLabelsMap $sourceTypeLabels,
        ZoteroSchemaLoader $zoteroSchemaLoader
    ) {
        $this->valueTypes = $valueTypes;
        $this->sourceTypeLabels = $sourceTypeLabels;
        $this->zoteroSchema = $zoteroSchemaLoader->load();

        foreach (self::$availableZoteroItemsTypes as $itemCode) {
            $this->addZoteroType($itemCode);
        }
    }

    public function getZoteroSourceType(string $code): SourceType
    {
        $itemType = $this->zoteroSchema->getItemType($code);
        $version = 'z.' . $this->zoteroSchema->version;
        $type = new SourceType([
            'code_name' => $code,
            'label'     => $this->sourceTypeLabels->mapSourceTypeLabel($code),
            'version'   => $version
        ]);

        $order = 0;
        foreach ($itemType->fields as $field) {
            $baseAttributeCode = $field->baseField;
            $attributeCode = $field->field;
            $valueType = $this->valueTypes->mapValueType($attributeCode);
            $attribute = new Attribute([
                'type'  => $valueType,
                'code'  => $attributeCode,
                'base'  => $baseAttributeCode,
                'label' => null,
                'order' => $order++
            ]);
            $type->pushAttribute($attributeCode, $attribute);
        }

        $roleCollection = new RoleCollection([
            'type' => $type
        ]);

        foreach ($itemType->creatorTypes as $creatorType) {
            $roleCode = $creatorType->creatorType;
            $roleCollection->pushAttribute(
                $roleCode,
                new Role([
                    'code' => $creatorType->creatorType,
                    'label' => '',
                    'primary' => (bool) $creatorType->primary
                ])
            );
        }
        $type->fill([
            'roles' => $roleCollection
        ]);

        return $type;
    }

    public function addZoteroType($code)
    {
        $this->sourceTypes[$code] = $this->getZoteroSourceType($code);
    }

    public function get($codeName): SourceType
    {
        return $this->sourceTypes[$codeName];
    }
}
