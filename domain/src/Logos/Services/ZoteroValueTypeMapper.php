<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Logos\Services\Zotero\LogosMapper;

/**
 * Maps the fields types of Zotero to Logos Value Types
 */
class ZoteroValueTypeMapper extends LogosMapper
{
    protected function boot(array $logosValueTypes): bool
    {
        $this->map = $logosValueTypes;

        $this->default = $logosValueTypes['default'];

        return true;
    }
}
