<?php

namespace App\Logos;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Utils;

// Hacer funciones estáticas
class Locale
{

    public $langPattern = "/^[a-z]{2}$/";

    public $langsSupported;

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
        // ddd($parameters);
        $newLocaleRoute = url()->toRoute(request()->route(), $parameters, false);
        return $newLocaleRoute;
    }

    /**
     * Remplaza el lenguaje en el path actual en la primera ocurrencia válida.
     * 
     * @param string $path
     * @param string $language
     * 
     * @return mixed new path if any valid language parameter are found. 
     *               Null otherwise.
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
}
