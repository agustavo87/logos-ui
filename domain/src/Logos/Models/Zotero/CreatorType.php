<?php

declare(strict_types=1);

namespace Arete\Logos\Models\Zotero;

use Arete\Common\FillableProperties;

class CreatorType extends FillableProperties
{
    public string $creatorType = '';
    public bool $primary = false;

    public function fillDefaultsAttributes()
    {
        //
    }
}
