<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Zotero;

use Arete\Common\FillableProperties;

class Field extends FillableProperties
{
    public ?string $field = null;
    public ?string $baseField = null;

    public function fillDefaultsAttributes()
    {
        //
    }
}
