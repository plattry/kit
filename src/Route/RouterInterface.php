<?php

declare(strict_types = 1);

namespace Plattry\Kit\Route;

use Closure;

/**
 * Describe a router instance.
 */
interface RouterInterface
{
    /**
     * Register routing rules.
     * @param string $path
     * @param array $middlewares
     * @param string|Closure $target
     * @return void
     */
    public function register(string $path, array $middlewares, string|Closure $target): void;

    /**
     * Parse requests to routing rules.
     * @param string $path
     * @param array|null $query
     * @return false|RuleInterface
     */
    public function parse(string $path, array &$query = null): RuleInterface|null;
}
