<?php

declare(strict_types=1);

namespace Arete\Common;

abstract class Provider
{
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    abstract public function register();

    public function boot()
    {
        //
    }
}
