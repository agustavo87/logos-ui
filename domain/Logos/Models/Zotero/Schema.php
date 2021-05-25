<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

use Arete\Common\FillableProperties;

class Schema extends FillableProperties
{
    public int $version;

    /**
     * @var ItemType[]
     */
    public array $itemTypes;

    public array $meta;

    public CSLMap $csl;

    public function fillDefaultsAttributes()
    {
        $this->defaultAttributes = [
            'version' => 0,
            'itemTypes' => [new ItemType()],
            'meta' => [],
            'csl' => new CSLMap()
        ];
    }
}
