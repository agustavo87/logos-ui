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
            return redirect($this->locale->replaceLocaleInCurrentURI($estimatedLocale));
        }
        
        app()->setLocale($uriLocale);
        return $next($request);
    }

}
