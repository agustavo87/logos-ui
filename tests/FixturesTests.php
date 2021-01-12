<?php

declare(strict_types=1);

namespace Tests;

/**
 * Allows the realization of task before and after all tests,
 * but with the Laravel App container available.
 */
trait FixturesTests
{
    use CreatesApplication;

    protected static $lastInstance = null;
    protected static bool $destroy = false;
    public static $initialized = false;

    /**
     * Acciones a realizar antes de todos los tests.
     *
     * @return void
     */
    protected static function beforeAll(): void
    {
        //
    }

    /**
     * Acciones a realizar luego de todos los test
     *
     * @return void
     */
    protected static function afterAll(): void
    {
        //
    }

    /**
     * Acciones a realizar antes de cada test
     *
     * @return void
     */
    public function beforeEach(): void
    {
        //
    }

    /**
     * Acciones a realizar luego de cada test
     *
     * @return void
     */
    public function afterEach(): void
    {
        //
    }

    /**
     * Maneja la inicialización de la app de Laravel
     *
     * Coordina los eventos para que las acciones before, after, etc.
     * se ejecuten correctamente, y con la app de laravel disponible.
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (static::$lastInstance !== null) {
            static::destroyApp();   // se cierra la instancia anterior para
                                    // crear una nueva
        }

        parent::setUp();            // se crea la app por lo que podemos acceder a sus recursos.
        if (!static::$initialized) {
            static::$initialized = true;
            static::beforeAll();    // Ocurre una vez nomás en un ciclo
        }
        $this->beforeEach();
    }

    /**
     * Da de baja la aplicación de modo controlado
     *
     * Previene la desactivación automática de Laravel, guarda una instancia,
     * y pasa el control a la próxima iteración de test, o al comando afterAll.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        /*
            se previene la destrucción de la app para permitir controlar los
            eventos afterEach y afterAll de todos modos se desactiva la
            instancia anterior justo en el comienzo del próximo ciclo en setUp
        */
        if (static::$destroy) {
            parent::tearDown();
        } else {
            $this->afterEach();
            static::$lastInstance = $this;  // Es necesario guardar una instancia para desactivarla
                                            // luego.
        }
    }

    /**
     * Da de baja la aplicación de modo controlado
     *
     * Previene la desactivación automática de Laravel, guarda una instancia,
     * y pasa el control a la próxima iteración de test, o al comando afterAll.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        // podemos hacer algunas limpiezas finales antes de terminar aún esta la
        // app abierta.
        // *Atención:* Acá debería ejecutarse también el afterEach() de la ultima
        // instancia.
        static::afterAll();
        static::destroyApp();
        static::$initialized = false; // por las dudas
    }

    /**
     * Destroys the laravel App
     *
     * @return void
     */
    protected static function destroyApp(): void
    {
        if (static::$lastInstance !== null) {
            static::$destroy = true;
            static::$lastInstance->tearDown();
            static::$destroy = false;
        }
    }
}
