<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Arete\Sofrosine\Services\Locale;
use Illuminate\Support\Facades\Validator;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Locale::class, function ($app) {
            return new Locale;
        });

        $this->app->alias(Locale::class, 'locale');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Locale $locale)
    {
        Validator::extend('language_valid', [$locale, 'validateValidLanguage']);
        Validator::extend('language_supported', [$locale, 'validateSupportedLanguage']);
    }


}
