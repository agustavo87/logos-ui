<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

use Arete\Common\FillableProperties;

use function Arete\Common\array_filter_keys;

class Schema extends FillableProperties
{
    public int $version;

    /**
     * @var ItemType[]
     */
    public array $itemTypes;

    public array $meta;

    public CSLMap $csl;

    public const DEFAULT_VALUE_TYPE = 'text';

    protected function fillDefaultsAttributes()
    {
        $this->defaultAttributes = [
        'version' => 0,
        'itemTypes' => [new ItemType()],
        'meta' => [],
        'csl' => new CSLMap()
        ];
    }

    /**
     * @param array $data
     *
     * @return self
     */
    public function addItemTypes(array $data, $reset = false): self
    {
        if ($reset) {
            $this->itemTypes = [];
        }

        foreach ($data as $itemAttributes) {
            $simpleAttributes = array_filter_keys($itemAttributes, ['itemType']);
            $itemType = new ItemType($simpleAttributes);
            foreach (array_filter_keys($itemAttributes, ['fields', 'creatorTypes']) as $attributeName => $values) {
                switch ($attributeName) {
                    case 'fields':
                        $itemType->addFields($values);
                        break;
                    case 'creatorTypes':
                        $itemType->addCreatorTypes($values);
                }
            }
            $this->itemTypes[] = $itemType;
        }
        return $this;
    }

    /**
     * Get the first ItemType that match $name
     *
     * @param string $name
     *
     * @return ItemType|null
     */
    public function getItemType(string $name): ?ItemType
    {
        $results = array_filter($this->itemTypes, fn ($item) => $item->itemType == $name);
        return count($results) ? array_shift($results) : null;
    }

    /**
     * Returns the registered value type in meta
     *
     * Or the default value ('text')
     *
     * @param Field $field
     *
     * @return string
     */
    public function valueType(Field $field): string
    {
        if (isset($this->meta['fields'][$field->field]['type'])) {
            $result = $this->meta['fields'][$field->field]['type'];
        };
        return  $result ?? self::DEFAULT_VALUE_TYPE;
    }
}
