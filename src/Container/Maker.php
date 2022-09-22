<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container;

use Closure;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionUnionType;
use RuntimeException;

/**
 * A maker instance.
 */
class Maker implements MakerInterface
{
    /**
     * The container instance.
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function make(string|object $resource, array $vars = []): object
    {
        if ($resource instanceof Closure) {
            return $this->invokeFunc($resource, $vars);
        }

        if (is_object($resource)) {
            return $resource;
        }

        return $this->invokeClass($resource, $vars);
    }

    /**
     * Invoke a closure by reflection.
     * @param Closure $name
     * @param array $vars
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    protected function invokeFunc(Closure $name, array $vars): object
    {
        $refFunc = new ReflectionFunction($name);

        $args = $this->getArgs($refFunc, $vars);

        return $refFunc->invokeArgs($args);
    }

    /**
     * Invoke a class by reflection.
     * @param string $name
     * @param array $vars
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    protected function invokeClass(string $name, array $vars): object
    {
        $refClass = new ReflectionClass($name);

        !$refClass->isInstantiable() &&
        throw new InvalidArgumentException("Invalid class, `$name` should be instantiable.");

        if (($refConstruct = $refClass->getConstructor()) === null)
            return $refClass->newInstanceWithoutConstructor();

        $args = $this->getArgs($refConstruct, $vars);

        return $refClass->newInstanceArgs($args);
    }

    /**
     * Get all arguments.
     * @param ReflectionFunctionAbstract $refFunc
     * @param array $vars
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getArgs(ReflectionFunctionAbstract $refFunc, array $vars = []): array
    {
        return array_map(
            fn($refParam) => $this->parseParameter($refParam, $vars),
            $refFunc->getParameters()
        );
    }

    /**
     * Parse a parameter.
     * @param ReflectionParameter $refParam
     * @param array $vars
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function parseParameter(ReflectionParameter $refParam, array $vars = []): mixed
    {
        $name = $refParam->getName();
        if (isset($vars[$name]))
            return $vars[$name];

        if ($refParam->hasType()) {
            $refType = $refParam->getType();
            if ($refType instanceof ReflectionUnionType) {
                $refTypes = $refType->getTypes();
            } else {
                $refTypes = [$refType];
            }

            foreach ($refTypes as $refSubType) {
                $typeName = $refSubType->getName();
                if (!$this->container->has($typeName))
                    continue;

                return $this->container->get($typeName);
            }

            if ($refType->allowsNull())
                return null;
        }

        if ($refParam->isDefaultValueAvailable())
            return $refParam->getDefaultValue();

        throw new RuntimeException("Invoke `$name` error due to missing argument.");
    }
}
