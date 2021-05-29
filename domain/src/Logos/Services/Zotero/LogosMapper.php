<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Logos\Services\MapperInterface;

class LogosMapper implements MapperInterface
{

    public array $map = [];

    public function __construct(array $logosValueTypes)
    {
        $this->map = [
            'default' => $logosValueTypes['default'],
            'date'    => $logosValueTypes['date']
        ];
    }
    public function mapValueType(string $codeName): ?string
    {
        return $this->map[$codeName] ?? $this->map['default'];
    }
}
