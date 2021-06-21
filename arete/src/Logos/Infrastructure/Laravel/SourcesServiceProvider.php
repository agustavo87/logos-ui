<?php

namespace Arete\Logos\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Arete\Logos\Application\LogosContainer as Logos;

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
            \Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository::class,
            \Arete\Logos\Infrastructure\Laravel\DBSourceTypeRepository::class
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository::class,
            \Arete\Logos\Infrastructure\Laravel\DBCreatorTypeRepository::class
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\LogosEnviroment::class,
            \Arete\Logos\Infrastructure\Laravel\LogosEnviroment::class
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\SourceRepository::class,
            function ($app) {
                return new \Arete\Logos\Infrastructure\Laravel\DBSourceRepository(
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository::class),
                    $app->make(\Arete\Logos\Domain\Schema::class),
                    $app->make(\Arete\Logos\Infrastructure\Laravel\Common\DB::class)
                );
            }
        );
        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\CreatorsRepository::class,
            function ($app) {
                return new \Arete\Logos\Infrastructure\Laravel\DBCreatorsRepository(
                    $app->make(\Arete\Logos\Infrastructure\Laravel\Common\DB::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository::class)
                );
            }
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository::class,
            \Arete\Logos\Infrastructure\Laravel\LvConfigurationRepository::class
        );

        // Binding of application services used by laravel adapters
        $this->app->bind(
            \Arete\Logos\Domain\Schema::class,
            function ($app) {
                return Logos::getOwn('schema');
            }
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\ZoteroSchemaLoaderInterface::class,
            function ($app) {
                return Logos::getOwn('zoteroSchema');
            }
        );

        $this->app->bind(
            \Arete\Logos\Application\Interfaces\ValueTypeMapper::class,
            function ($app) {
                return Logos::getOwn('valueTypes');
            }
        );

        $this->app->bind(
            \Arete\Logos\Application\Interfaces\MapsSourceTypeLabels::class,
            function ($app) {
                return Logos::getOwn('sourceTypeLabels');
            }
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
