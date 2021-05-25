<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

use Arete\Common\FillsProperties;

class Schema
{
    use FillsProperties;

    public int $version;

    /**
     * @var ItemType[]
     */
    public array $itemTypes;

    public array $meta;

    public CSLMap $csl;

    protected $defaultAttributes = [];

    public function __construct(?array $attributes = [])
    {
        $this->fillDefaultsAttributes();
        $this->fill($attributes);
    }

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
