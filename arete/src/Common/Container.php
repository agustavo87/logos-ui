<?php

declare(strict_types=1);

namespace Arete\Common;

use Psr\Container\ContainerInterface;
use Arete\Exceptions\BindingNotFoundException;

/**
 * Container of recipes to deliver Instances of Classes
 * 
 * @todo write some unit tests.
 */
class Container
{
    /**
     * Containment constants
     */
    public const HAS_OWN_ABSTRACT = 1;  // The abstract class has a concrete binding registered in this container
    public const HAS_EXTERNAL = 3;      // The abstract has a binding but in an external (delegated) container
    public const HAS_ALIAS = 2;         // It has an alias registered
    public const NOT_REGISTERED = 0;    // The abstract is not registered

    /**
     * External container for delegation
     *
     * @var ContainerInterface|null
     */
    protected static ?ContainerInterface $external = null;

    /**
     * Container bindings
     *
     * @var array
     */
    protected static array $bindings = [];

    /**
     * Alias of bindings
     *
     * @var array
     */
    protected static array $alias = [];

    /**
     * Providers of bindngs and clasess
     * 
     * @var Provider[]
     */
    protected static array $providers = [];

    /**
     * Inyects a container for delegation
     *
     * @param ContainerInterface $container
     * @return void
     */
    public static function delegate(ContainerInterface $container)
    {
        static::$external = $container;
    }

    /**
     * Gets a class by its id
     * 
     * It search in this container and external (delegated) if specified
     *
     * @param string $id
     * @return void
     */
    public static function get(string $id)
    {
        switch (static::hasAny($id)) {
            case self::HAS_OWN_ABSTRACT:
                return static::resolveOwn($id);
            case self::HAS_ALIAS:
                return static::resolveOwn(static::$alias[$id]);
            case self::HAS_EXTERNAL:
                return static::resolveExternal($id);
        }
        throw new BindingNotFoundException('No binding with id: \'' . $id . '\'');
    }

    /**
     * Get without delegate
     *
     * @param mixed $id
     *
     * @return mixed
     * @throws BindingNotFoundException
     */
    public static function getOwn($id)
    {
        switch (static::hasAny($id)) {
            case self::HAS_OWN_ABSTRACT:
                return static::resolveOwn($id);
            case self::HAS_ALIAS:
                return static::resolveOwn(static::$alias[$id]);
        }
        throw new BindingNotFoundException('No binding with id: \'' . $id . '\'');
    }

    /**
     * Respond if the $id is registered
     *
     * If $kind is specified by containment constant returns
     * if the containment is equal to that kind.
     *
     * @param string $id
     * @param int|null $kind containment constant
     *
     * @return void
     */
    public static function has(string $id, ?int $kind = null)
    {
        $status = static::hasAny($id);
        return $kind ? $kind == $status : (bool) $status;
    }

    /**
     * Has own abstract or alias binding, or external binding.
     *
     * @param string $id
     *
     * @return void
     */
    protected static function hasAny(string $id): int
    {
        if (static::hasAlias($id)) {
            return self::HAS_ALIAS;
        } elseif (static::hasAbstract($id)) {
            return self::HAS_OWN_ABSTRACT;
        } elseif (static::hasExternal($id)) {
            return self::HAS_EXTERNAL;
        }
        return self::NOT_REGISTERED;
    }

    /**
     *
     * @param string $abstract
     *
     * @return mixed
     */
    protected static function resolveOwn(string $abstract)
    {
        return call_user_func(static::$bindings[$abstract], static::class);
    }

    /**
     * @param string $abstract
     *
     * @return object|boolean
     */
    protected static function resolveExternal(string $abstract)
    {
        return static::$external->get($abstract);
    }

    /**
     * Has own alias.
     *
     * @param mixed $alias
     *
     * @return bool
     */
    protected static function hasAlias($alias): bool
    {
        return array_key_exists($alias, static::$alias);
    }

    /**
     * Has own abstract.
     *
     * @param mixed $abstract
     *
     * @return bool
     */
    protected static function hasAbstract($abstract): bool
    {
        return array_key_exists($abstract, static::$bindings);
    }

    /**
     * Check external container has id.
     *
     * @param mixed $id
     *
     * @return bool
     */
    protected static function hasExternal($id): bool
    {
        return static::$external ? static::$external->has($id) : false;
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

    /**
     * Set providers of recipes
     * 
     * @param Provider[] $providers
     *
     * @return void
     */
    public static function setProviders(array $providers)
    {
        static::$providers = $providers;
    }

    /**
     * Push a provider of recipes
     *
     * @param string $provider
     * @return void
     */
    public static function pushProvider(string $provider)
    {
        static::$providers[] = $provider;
    }
}
