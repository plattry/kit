<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container;

use Plattry\Kit\Container\Exception\ContainerException;
use Plattry\Kit\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionException;

/**
 * A container instance.
 */
class Container implements ContainerInterface
{
    /**
     * The global container instance.
     * @var ContainerInterface|null
     */
    protected static ContainerInterface|null $global = null;

    /**
     * The shared resources.
     * @var array
     */
    protected static array $bundle = [];

    /**
     * The object pool.
     * @var object[]
     */
    protected array $pool = [];

    /**
     * The object maker.
     * @var Maker
     */
    protected Maker $maker;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->pool[ContainerInterface::class] = $this;
        $this->maker = new  Maker($this);
    }

    /**
     * Copy all resources from current container to a new one.
     * @return void
     */
    public function __clone(): void
    {
        $this->pool[ContainerInterface::class] = $this;
        $this->maker = new  Maker($this);
    }

    /**
     * Set a container instance as global container.
     * @param ContainerInterface $container
     * @return void
     */
    public static function setGlobal(ContainerInterface $container): void
    {
        self::$global = $container;
    }

    /**
     * Get the global container instance.
     * @return ContainerInterface
     */
    public static function getGlobal(): ContainerInterface
    {
        if (self::$global instanceof ContainerInterface) {
            return self::$global;
        }

        return self::$global = new self();
    }

    /**
     * Set the bundle resources.
     * @param string|array $bundleOrName
     * @param string|object|null $resource
     * @throws ContainerException
     */
    public static function setBundle(string|array $bundleOrName, string|object $resource = null): void
    {
        if (is_array($bundleOrName)) {
            self::$bundle = $bundleOrName;

            return;
        }

        if (is_object($resource) || class_exists($resource)) {
            self::$bundle[$bundleOrName] = $resource;

            return;
        }

        throw new ContainerException("Invalid container bundle, `$resource` should be a class, object or closure.");
    }

    /**
     * Get the bundle resources.
     * @param string|null $name
     * @return string|array|object
     * @throws ContainerException
     */
    public static function getBundle(string $name = null): string|array|object
    {
        if (is_null($name))
            return self::$bundle;

        if (isset(self::$bundle[$name]))
            return self::$bundle[$name];

        throw new ContainerException("Not found bundle `$name`.");
    }

    /**
     * Set an object with $id.
     * @param string $id
     * @param object $object
     * @return void
     */
    public function set(string $id, object $object): void
    {
        $this->pool[$id] = $object;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return isset($this->pool[$id]) || isset(self::$bundle[$id]);
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function get(string $id): object
    {
        if (isset($this->pool[$id]))
            return $this->pool[$id];

        if (isset(self::$bundle[$id])) {
            return $this->pool[$id] = $this->maker->make(self::$bundle[$id]);
        }

        throw new NotFoundException("Not found resource `$id`.");
    }
    
    /**
     * Clear all objects in pool.
     * @return void
     */
    public function clear(): void
    {
        $this->pool = [];
    }
}
