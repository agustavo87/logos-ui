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
    public function addItemTypes(array $data): self
    {
        foreach ($data as $itemAttributes) {
            $simpleAttributes = array_filter_keys($itemAttributes, ['itemType']);
            $itemType = new ItemType($simpleAttributes);
            foreach (array_filter_keys($itemAttributes, ['fields', 'creatorTypes']) as $attribueName => $values) {
                switch ($attribueName) {
                    case 'fields':
                        $itemType->addFields($values);
                        break;
                    case 'creatorTypes':
                        $itemType->addCreatorTypes($values);
                }
            }
        }
        return $this;
    }
}
