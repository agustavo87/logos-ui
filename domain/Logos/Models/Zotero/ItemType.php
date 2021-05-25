<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

class ItemType
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
}
