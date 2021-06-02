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
        $this->mergeConfigFrom(__DIR__  . '/../../../config/logos.php', 'logos');

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

        $this->app->bind(
            \Arete\Logos\Services\Zotero\LogosMapper::class,
            function ($app) {
                return new \Arete\Logos\Services\Zotero\LogosMapper(config('logos.valueTypes'));
            }
        );

        $this->app->bind(
            \Arete\Logos\Services\ZoteroValueTypeMapper::class,
            function ($app) {
                return new \Arete\Logos\Services\ZoteroValueTypeMapper(config('logos.fieldValueTypes'));
            }
        );

        $this->app->bind(
            \Arete\Logos\Repositories\SourceTypeRepositoryInterface::class,
            \Arete\Logos\Repositories\DBSourceTypeRepository::class
        );
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../config/logos.php' => config_path('logos.php'),
        ]);
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
