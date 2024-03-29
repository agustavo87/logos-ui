<?php

namespace Arete\Logos\Infrastructure\Laravel;

use Illuminate\Support\ServiceProvider;
use Arete\Logos\Application\LogosContainer as Logos;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesTranslator;
use Arete\Logos\Domain\SimpleFormatter;
use Arete\Logos\Infrastructure\Laravel\Commands\PublishSourcesJsAssets;
use Arete\Logos\Infrastructure\Laravel\Common\DB;

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
            \Arete\Logos\Application\Ports\Interfaces\CreateSourceUC::class,
            function ($app) {
                return new CreateSourceUC(
                    $app->make(DB::class),
                    $app->make(SourcesRepository::class),
                    $app->make(CreatorsRepository::class),
                    $app->make(SourcesTranslator::class)
                );
            }
        );

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\SourcesTranslator::class,
            \Arete\Logos\Infrastructure\Laravel\SimpleSourcesTranslator::class
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
            \Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository::class,
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

        $this->app->bind(
            \Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase::class,
            function ($app) {
                return Logos::getOwn(\Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase::class);
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishSourcesJsAssets::class
            ]);
        }
        $this->publishes([
            __DIR__  . '/config/sources.php' => config_path('sources.php'),
        ]);

        $this->loadViewsFrom(__DIR__  . '/resources/views', 'logos');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'logos');
    }
}
