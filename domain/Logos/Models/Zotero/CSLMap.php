<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

use Arete\Common\FillableProperties;

class CSLMap extends FillableProperties
{
    public array $types = [];

    public array $fields = [];

    public array $names = [];

    public function fillDefaultsAttributes()
    {
        //
    }
}
