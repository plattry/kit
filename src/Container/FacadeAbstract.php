<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * A abstract facade instance.
 */
abstract class FacadeAbstract
{
    /**
     * Create the instance of actually invoking method.
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function run(): object
    {
        $name = static::getCallName();

        if (!Container::getGlobal()->has($name)) {
            Container::setBundle($name, static::getCallClass());
        }

        return Container::getGlobal()->get($name);
    }

    /**
     * Get the object name of actually invoking method.
     * @return string
     */
    abstract public static function getCallName(): string;

    /**
     * Get the class or object of actually invoking method.
     * @return string|object
     */
    abstract public static function getCallClass(): string|object;

    /**
     * Call the normal method statically.
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function __callStatic(string $method, array $args = []): mixed
    {
        return call_user_func_array([static::run(), $method], $args);
    }
}
