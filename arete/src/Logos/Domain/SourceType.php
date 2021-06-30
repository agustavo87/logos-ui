<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Common\FillsProperties;
use Arete\Logos\Domain\Abstracts\SourceType as AbstractSourceType;

class SourceType extends AbstractSourceType
{
    use FillsProperties;

    public function __construct(array $porperties = [])
    {
        $this->fill($porperties);
    }
}
