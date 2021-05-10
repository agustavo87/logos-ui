<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Arete\Sofrosine\Support\Utils;

// Hacer funciones estáticas
class Locale
{
    public $langPattern = "/^[a-z]{2}$/";

    public $langsSupported;

    public $HTTPlanguageConsidered = false;

    public function __construct()
    {
        $this->langsSupported = config('locale.languages.supported');
    }

    public function showRequest()
    {
        return request();
    }

    public function replaceLocaleInCurrentURI(string $locale)
    {
        $parameters = Route::getCurrentRoute()->originalParameters();
        $parameters['locale'] = $locale;
        $newLocaleRoute = url()->toRoute(request()->route(), $parameters, false);
        return $newLocaleRoute;
    }

    /**
     * Remplaza el lenguaje en el path actual en la primera ocurrencia válida.
     * 
     * @param   string  $path
     * @param   string  $language
     * @return  mixed   new path if any valid language parameter are found. 
     *                  Null otherwise.
     */
    public function replaceLanguageInPath(string $path, string $language): ?string
    {
        if (!($this->isValid($language) && $this->supported($language))) {
            // error
            return null;
        }

        return Utils::replaceFirstSegment(
            $path, 
            $language, 
            fn ($v) => $this->isValid($v)
        ) ?? $path;
    }

    public function inURL()
    {
        $segment = request()->segment(1);
        return $this->isValid($segment) ? $segment : null;
    }

    public function isValid($locale)
    {
        return preg_match($this->langPattern, $locale) ? true : false;
    }

    public function supported($locale)
    {
        return in_array($locale, $this->langsSupported);
    }

    public function getSupportedUriLocale()
    {
        return $this->supported(
            $inUri = $this->inURL()
        ) ? $inUri : false;
    }

    /**
     * Validator 'language_valid' rule
     *
     * @return bool
     */
    public function validateValidLanguage($attribute, $value, $parameters, $validator)
    {
        return $this->isValid($value);
    }

    /**
     * Validator 'language_supported' rule
     *
     * @return bool
     */
    public function validateSupportedLanguage($attribute, $value, $parameters, $validator)
    {
        return $this->supported($value);
    }

    public function getLocale() 
    {    
        if (Auth::check()) {
            return Auth::user()->language;
        } else if (session()->has('language')) {
            return session('language');
        } else if ($inUri = $this->getSupportedUriLocale()) {
            return $inUri;
        } else if ($inHTTP = $this->getBestAvailableLocaleFromHTTP()) {
            $this->HTTPlanguageConsidered = true;
            return $inHTTP['language'];
        }
        return config('locale.languages.default');
    }

    /**
     * Secciona el valor de un header Accept-Language
     * 
     * Schema: fr-CH
     * [ 'fr-CH' => [
     *  'language' => 'fr',
     *  'subtags' => [ 'fr', 'CH' ],
     *  'q' => null
     * ]
     * 
     * @param string $httpLangs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function bysectHTTPLocale($httpLangs)
    {
        $tags = collect( Utils::array_trim(explode(',', $httpLangs)) ) ;
        $tags = $tags->mapWithKeys(function ($tag) {
            $sect = Utils::array_trim(explode(';', $tag));
            $subtags = Utils::array_trim(explode('-', $sect[0]));
            if (count($sect) > 1) {
                return [
                    $sect[0] => [
                        'language' => $subtags[0],
                        'subtags' => $subtags,
                        'q' => (float) explode('=', $sect[1])[1]
                    ]
                ];
            }
            return [
                $sect[0] => [
                    'language' => $subtags[0],
                    'subtags' => $subtags,
                    'q' => null
                ]
            ];

        });
    
        return $tags;
    }

    /**
     * Parsea el contenido de HTTP Accept-Language
     * 
     * @param   string  $httpLangs
     * @return  \Illuminate\Database\Eloquent\Collection
     */
    public function parseHTTPLocale ($httpLangs)
    {
        $tags = $this->bysectHTTPLocale($httpLangs);
        $tags = $tags->sortBy(function ($tag, $key) {
            return $tag['q'] ? $tag['q'] : 1 ;
        }, SORT_REGULAR, true);
        return $tags;

    }

    /**
     * Devuelve la información de lenguaje de la solicitud HTTP
     * 
     * @param   bool $first Si devolver solo el primer valor o toda la colección.
     * @return  \Illuminate\Database\Eloquent\Collection|array|null
     */
    public function getLocaleFromHTTP(bool $first = false)
    {
        if (!request()->hasHeader('Accept-Language')) return null;

        $locales =  $this->parseHTTPLocale(request()->header('Accept-Language'));
        return $first ? $locales->first() : $locales;
    }

    /**
     * Devuelve locale disponible de las preferencias HTTP o null
     * 
     * @return array
     */
    public function getBestAvailableLocaleFromHTTP()
    {
        $locales = $this->getLocaleFromHTTP();
        $compatible = $locales->first(function ($locale) {
            return in_array($locale['language'], ['en']);
        });

        return $compatible;
    }
}
