<?php

declare(strict_types = 1);

namespace Plattry\Kit\Route;

use Closure;

/**
 * Describe a rule instance.
 */
interface RuleInterface
{
    /**
     * Get the request path.
     * @return string
     */
    public function getPath(): string;

    /**
     * Get the middlewares.
     * @return array
     */
    public function getMiddlewares(): array;

    /**
     * Get the request target.
     * @return string|Closure
     */
    public function getTarget(): string|Closure;
}
