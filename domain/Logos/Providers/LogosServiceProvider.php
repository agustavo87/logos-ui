<?php

namespace Arete\Logos\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Arete\Logos\Services\Sources;

class LogosServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /** @todo vincular a una interfaz más bien */
        $this->app->bind(Sources::class, function ($app) {
            return new Sources();
        });

        $this->app->alias(Sources::class, 'sources');

        if ($this->app->environment('testing')) {
            $this->app->bind(
                \Arete\Logos\Services\Zotero\SchemaLoaderInterface::class,
                \Arete\Logos\Services\Zotero\SimpleSchemaLoader::class
            );
        }
    }



     /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Sources::class];
    }
}
