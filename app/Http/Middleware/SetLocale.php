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
        $locale = $uriLocale;

        if (Auth::check()) {
            $locale = Auth::user()->language;
            if ($locale != $uriLocale) {
                return redirect($this->locale->replaceLocaleInCurrentURI($locale));
            }
        } elseif ($request->session()->has('language')) {
            $locale = $request->session()->get('language');
            if ( $locale != $uriLocale) {
                return redirect($this->locale->replaceLocaleInCurrentURI($locale));
            }
        } else {
            if (!$this->locale->supported($uriLocale)) {
                return redirect($this->locale->replaceLocaleInCurrentURI(config('locale.languages.default')));
            }
        }

        app()->setLocale($locale);
        return $next($request);
    }
}
