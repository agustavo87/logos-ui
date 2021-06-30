<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Common\FillsProperties;
use Arete\Logos\Domain\Abstracts\CreatorType as AbstractCreatorType;

class CreatorType extends AbstractCreatorType
{
    use FillsProperties;

    public function __construct(array $properties = [])
    {
        $this->fill($properties);
    }
}
