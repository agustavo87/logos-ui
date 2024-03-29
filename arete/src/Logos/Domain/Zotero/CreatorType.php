<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Zotero;

use Arete\Common\FillableProperties;

class CreatorType extends FillableProperties
{
    public ?string $creatorType = null;
    public bool $primary = false;

    public function fillDefaultsAttributes()
    {
        //
    }
}
