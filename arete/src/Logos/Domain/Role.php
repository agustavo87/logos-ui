<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Common\FillableProperties;

class Role extends FillableProperties
{
    public string $code = '';
    public ?string $label = null;
    public bool $primary = false;

    protected function fillDefaultsAttributes()
    {
        //
    }

    public function __toString()
    {
        return $this->code;
    }
}
