<?php

declare(strict_types=1);

namespace Arete\Common;

use Psr\Container\ContainerInterface;
use Arete\Exceptions\BindingNotFoundException;

class Container
{
    protected static ?ContainerInterface $external = null;

    protected static array $bindings = [];

    protected static array $alias = [];

    /**
     * @var Provider[]
     */
    protected static array $providers = [];

    public static function delegate(ContainerInterface $container)
    {
        static::$external = $container;
    }

    public static function get(string $id)
    {
        if (static::has($id)) {
            return static::resolve($id);
        } else {
            throw new BindingNotFoundException('No binding with id: \'' . $id . '\'');
        }
    }

    public static function has(string $id)
    {
        return array_key_exists($id, static::$bindings) ? true
                : (static::$external ? static::$external->has($id) : false);
    }

    protected static function resolve(string $id)
    {
        return array_key_exists($id, static::$bindings)
            ? call_user_func(static::$bindings[$id], static::class)
            : (
                static::$external
                    ? (static::$external->has($id) ?  static::$external->get($id) : null)
                    : null
            );
    }

    public static function register($id, callable $recipe)
    {
        static::$bindings[$id] = $recipe;
    }

    public static function load()
    {
        $instances = [];
        foreach (static::$providers as $provider) {
            $instance = new $provider();
            $instance->setContainer(static::class);
            $instance->register();
            $instances[] = $instance;
        }

        foreach ($instances as $instance) {
            $instance->boot();
        }
    }

    public static function __callStatic($name, $arguments)
    {
        if (array_key_exists($name, static::$alias)) {
            $method = static::$alias[$name];
            if (count($arguments)) {
                return static::get($method)(...$arguments);
            } else {
                return static::get($method);
            }
        } else {
            throw new \BadMethodCallException("Method don't exist", 1);
        }
    }
}
