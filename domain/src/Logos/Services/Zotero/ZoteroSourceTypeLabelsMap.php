<?php

declare(strict_types=1);

namespace Arete\Logos\Services\Zotero;

use Arete\Common\Mapper;
use Arete\Logos\Services\MapsSourceTypeLabels;

/**
 * Maps Zotero Item Types to Logos Labels.
 */
class ZoteroSourceTypeLabelsMap extends Mapper implements MapsSourceTypeLabels
{
    protected function boot(array $data): bool
    {
        $notDefault = array_filter(
            $data,
            fn ($value, $key) => $key !== 'default',
            ARRAY_FILTER_USE_BOTH
        );
        $this->map = $notDefault;
        $this->default = $data['default'] ?? null;
        return true;
    }

    public function mapSourceTypeLabel(string $codeName): ?string
    {
        return $this->map($codeName);
    }
}
