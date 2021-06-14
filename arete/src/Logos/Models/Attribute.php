<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Common\FillableProperties;

class Attribute extends FillableProperties
{
    public string $code = '';
    public ?string $base  = null;
    public ?string $label = null;
    public string $type = '';
    public int $order = 0;

    protected function fillDefaultsAttributes()
    {
        //
    }
}
