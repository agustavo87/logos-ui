<?php

declare(strict_types=1);

namespace Arete\Logos\Adapters\Laravel;

use Arete\Common\Mapper;
use Arete\Logos\Ports\Interfaces\ValueTypeMapper;

/**
 * Maps value fields accoring to zotero specifications.
 */
class LogosMapper extends Mapper implements ValueTypeMapper
{
    protected function boot(array $logosValueTypes): bool
    {
        $this->map = [
            'date'    => $logosValueTypes['date']
        ];
        $this->default = $logosValueTypes['default'];

        return true;
    }

    public function mapValueType(string $codeName): ?string
    {
        return $this->map($codeName);
    }
}
