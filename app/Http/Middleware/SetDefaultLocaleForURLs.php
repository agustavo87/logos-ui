<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Logos\Locale;


class SetDefaultLocaleForURLs
{
    public $locale;

    public function __construct(Locale $locale) {
        $this->locale = $locale;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        $locale = config('locale.languages.default');

        if (Auth::check()) {
            $locale = Auth::user()->language;
        } elseif ($request->session()->has('language')) {
            $locale = session('language');
        } else {
            $uriLocale = $this->locale->inURL();
            if ($this->locale->supported($uriLocale)) {
                $locale = $uriLocale;
            }
        }

        URL::defaults([
            'locale' => $locale
        ]);

        return $next($request);
    }
    
}
