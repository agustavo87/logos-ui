<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

use Arete\Common\FillableProperties;

class ItemType extends FillableProperties
{
    public string $itemType;
    /**
     * @var Field[]
     */
    public array $fields;

    /**
     * @var CreatorType[]
     */
    public array $creatorTypes;

    public function fillDefaultsAttributes()
    {
        $this->defaultAttributes = [
            'itemType' => '',
            'fields' => [],
            'creatorTypes' => []
        ];
    }
}
