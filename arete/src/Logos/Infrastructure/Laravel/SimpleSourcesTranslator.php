<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\SourcesTranslator;

class SimpleSourcesTranslator implements SourcesTranslator
{
    public function translate(string $code, string $group = ''): string
    {
        $group = $group == '' ? '' : '.' . $group;
        return __("logos::sources{$group}.{$code}");
    }

    public function setLocale(string $locale)
    {
        //
    }
}
