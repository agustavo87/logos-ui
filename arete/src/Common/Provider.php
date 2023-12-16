<?php

declare(strict_types=1);

namespace Arete\Common;

abstract class Provider
{
    /**
     * The container
     */
    protected $container;

    /**
     * Method for inyecting the container
     *
     * @param $container
     * @return void
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Register bindings
     *
     * @return void
     */
    abstract public function register();

    /**
     * Initializes Classes after the bindings are registered
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
