<?php

declare(strict_types=1);

namespace Arete\Logos\Application\Ports\Interfaces;

interface SourcesTranslator
{
    /**
     * Translate source type codes
     *
     * @param string $code
     *
     * @return string|null
     */
    public function translate(string $code, string $group = ''): ?string;

    /**
     * Sets the _static_ locale for the translator
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale(string $locale);
}
