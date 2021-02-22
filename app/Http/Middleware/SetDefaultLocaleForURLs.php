<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Services\Locale;


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

        URL::defaults([
            'locale' => $this->locale->getLocale()
        ]);

        return $next($request);
    }
    
}
