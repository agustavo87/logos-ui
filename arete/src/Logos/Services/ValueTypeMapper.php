<?php

declare(strict_types=1);

namespace Arete\Logos\Services;

use Arete\Logos\Abstracts\ValueTypeMapper as BaseValueTypeMapper;
use Arete\Logos\Services\LogosContainer as Logos;
use Arete\Logos\Ports\Abstracts\ConfigurationRepository;

/**
 * Maps the fields types of Zotero to Logos Value Types
 */
class ValueTypeMapper extends BaseValueTypeMapper
{
    protected array $map;
    protected string $default;

    public function __construct(ConfigurationRepository $config)
    {
        $attributesValueTypes = $config('attributesValueTypes');
        $this->map = $attributesValueTypes['map'];
        $this->default = $attributesValueTypes['default'];
    }

    public function mapValueType(string $codeName): ?string
    {
        return $this->map[$codeName] ?? $this->default;
    }
}
