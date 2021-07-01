<?php

namespace Arete\Logos\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Arete\Logos\Application\LogosContainer as Logos;
use Arete\Logos\Domain\SimpleFormatter;

class SourcesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__  . '/config/sources.php', 'sources');

        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

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
            \Arete\Logos\Application\Ports\Interfaces\ParticipationRepository::class,
            \Arete\Logos\Infrastructure\Laravel\DBParticipationRepository::class
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\SourcesRepository::class,
            function ($app) {
                return new \Arete\Logos\Infrastructure\Laravel\DBSourcesRepository(
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\CreatorsRepository::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\CreatorTypeRepository::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\ParticipationRepository::class),
                    new SimpleFormatter(),
                    $app->make(\Arete\Logos\Domain\Schema::class),
                    $app->make(\Arete\Logos\Infrastructure\Laravel\Common\DB::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\LogosEnviroment::class)
                );
            }
        );
        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\CreatorsRepository::class,
            function ($app) {
                return new \Arete\Logos\Infrastructure\Laravel\DBCreatorsRepository(
                    $app->make(\Arete\Logos\Infrastructure\Laravel\Common\DB::class),
                    $app->make(\Arete\Logos\Application\Ports\Interfaces\LogosEnviroment::class),
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
}
