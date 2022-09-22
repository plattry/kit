<?php

declare(strict_types = 1);

namespace Plattry\Kit\Route;

use Closure;
use InvalidArgumentException;

/**
 * A rule instance.
 */
class Rule implements RuleInterface
{
    /**
     * The request path.
     * @var string
     */
    protected string $path;

    /**
     * The middlewares.
     * @var array
     */
    protected array $middlewares;

    /**
     * The request target.
     * @var string|Closure
     */
    protected string|Closure $target;

    /**
     * The constructor.
     */
    public function __construct(string $path, array $middlewares, string|Closure $target)
    {
        $this->path = (string)preg_replace(["# +#", "#/+#"], ["", "/"], $path);

        foreach ($middlewares as $middleware) {
            !is_string($middleware) && !$middleware instanceof Closure &&
            throw new InvalidArgumentException("Invalid middleware, which should be a class or callback.");
        }

        $this->middlewares = $middlewares;

        $this->target = $target;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @inheritDoc
     */
    public function getTarget(): string|Closure
    {
        return $this->target;
    }
}
