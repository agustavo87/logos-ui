<?php

namespace Arete\Logos\Adapters\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Arete\Logos\Services\LogosContainer as Logos;

class SourcesServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__  . '/../../../../../config/sources.php', 'sources');

        $this->app->bind(
            \Arete\Logos\Ports\Interfaces\SourceTypeRepository::class,
            \Arete\Logos\Adapters\Laravel\DBSourceTypeRepository::class
        );

        $this->app->bind(
            \Arete\Logos\Ports\Interfaces\CreatorTypeRepository::class,
            \Arete\Logos\Adapters\Laravel\DBCreatorTypeRepository::class
        );

        $this->app->bind(
            \Arete\Logos\Ports\Interfaces\LogosEnviroment::class,
            \Arete\Logos\Adapters\Laravel\LogosEnviroment::class
        );

        $this->app->bind(
            \Arete\Logos\Ports\Interfaces\SourceRepository::class,
            function ($app) {
                return new \Arete\Logos\Adapters\Laravel\DBSourceRepository(
                    $app->make(\Arete\Logos\Ports\Interfaces\SourceTypeRepository::class),
                    $app->make(\Arete\Logos\Ports\Interfaces\CreatorTypeRepository::class),
                    $app->make(\Arete\Logos\Adapters\Laravel\Common\DB::class)
                );
            }
        );

        $this->app->bind(
            \Arete\Logos\Ports\Abstracts\ConfigurationRepository::class,
            \Arete\Logos\Adapters\Laravel\LvConfigurationRepository::class
        );

        Logos::load();
        Logos::delegate($this->app);
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../../../config/sources.php' => config_path('sources.php'),
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
