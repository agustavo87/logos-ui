<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Logos\Abstracts\ValueTypeMapper as BaseValueTypeMapper;
use Arete\Logos\Services\LogosContainer as Logos;

/**
 * Maps the fields types of Zotero to Logos Value Types
 */
class ValueTypeMapper extends BaseValueTypeMapper
{
    protected array $map;
    protected string $default;

    public function __construct()
    {
        $attributesValueTypes = Logos::config('attributesValueTypes');
        $this->map = $attributesValueTypes['map'];
        $this->default = $attributesValueTypes['default'];
    }

    public function mapValueType(string $codeName): ?string
    {
        return $this->map[$codeName] ?? $this->default;
    }
}
