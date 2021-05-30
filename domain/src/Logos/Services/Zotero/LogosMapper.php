<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Common\Mapper;
use Arete\Logos\Services\ValueTypeMapperInterface;

class LogosMapper extends Mapper implements ValueTypeMapperInterface
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
