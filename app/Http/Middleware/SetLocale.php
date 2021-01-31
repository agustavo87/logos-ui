<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Logos\Locale;

class SetLocale
{

    public $locale;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Remplaza el lenguaje de la URI por otros criterios de mayor prioridad,
     * si existen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uriLocale = $this->locale->inURL();
        
        $estimatedLocale = $this->locale->getLocale();

        if ($uriLocale !== $estimatedLocale) {
            $response = redirect($this->locale->replaceLocaleInCurrentURI($estimatedLocale))
                            ->header('Content-Language', $estimatedLocale);
            if ($this->locale->HTTPlanguageConsidered) $response->header('Vary', 'Accept-Language');
            
            return $response;
        }
        
        app()->setLocale($uriLocale);

        $response = $next($request);

        $response->header('Content-Language', $uriLocale);
            // ->header('Vary', 'Accept-Language');

        return $response;
    }

    public function setLocaleHeaders(Request $request, string $locale)
    {
        
    }

}
