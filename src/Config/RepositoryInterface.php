<?php

declare(strict_types=1);

namespace Plattry\Kit\Config;

/**
 * Describe a config repository instance.
 */
interface RepositoryInterface
{
    /**
     * Import resource.
     * @param string|array|callable $resource
     * @param bool $recursive
     * @return void
     */
    public function import(string|array|callable $resource, bool $recursive = false): void;

    /**
     * Determine whether the config option is exist.
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the config option with $name.
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed;
}
