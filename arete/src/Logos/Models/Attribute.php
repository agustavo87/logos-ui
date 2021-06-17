<?php

declare(strict_types=1);

namespace Arete\Logos\Models;

use Arete\Common\FillableProperties;

class Attribute extends FillableProperties
{
    /**
     * @var string attribute code name
     */
    public string $code = '';

    /**
     * @var string|null base attribute code name
     */
    public ?string $base  = null;

    /**
     * @var string|null optional label
     */
    public ?string $label = null;

    /**
     * @var string value type
     */
    public string $type = '';

    /**
     * @var int order of presentation
     */
    public int $order = 0;

    protected function fillDefaultsAttributes()
    {
        //
    }
}
