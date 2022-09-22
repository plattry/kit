<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http;

use Closure;
use Plattry\Kit\Container\Container;
use Plattry\Kit\Container\ContainerAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A http request handler instance.
 */
class Handler implements RequestHandlerInterface
{
    use ContainerAwareTrait;

    /**
     * The middlewares.
     * @var array
     */
    protected array $middlewares;

    /**
     * The target.
     * @var string|Closure
     */
    protected string|Closure $target;

    /**
     * The construct.
     */
    public function __construct(array $middlewares, string|Closure $target)
    {
        $this->middlewares = $middlewares;
        $this->target = $target;
    }

    /**
     * Get the container.
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = current($this->middlewares);
        if ($middleware === false)
            return $this->call($request);

        next($this->middlewares);

        return $this->container->get($middleware)->process($request, $this);
    }

    /**
     * Call the target.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function call(ServerRequestInterface $request): ResponseInterface
    {
        if (is_string($this->target)) {
            [$class, $method] = explode("@", $this->target, 2);

            return $this->container->get($class)->{$method}($request);
        }

        return call_user_func($this->target, $request, $this->container);
    }
}
