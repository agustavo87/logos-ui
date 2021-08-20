<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\Ports\Interfaces\SourcesTranslator;

class SimpleSourcesTranslator implements SourcesTranslator
{
    public function translate(string $code, string $group = ''): ?string
    {
        $group = $group == '' ? '' : '.' . $group;
        $path = "logos::sources{$group}.{$code}";
        $trans = __($path);
        return $trans != $path ? $trans : null;
    }

    public function setLocale(string $locale)
    {
        //
    }
}
