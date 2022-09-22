<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Cookie;

use InvalidArgumentException;

/**
 * Describe a http cookie instance.
 */
interface CookieInterface
{
    /**
     * Generate a new cookie record and put it in queue.
     * @param string $name
     * @param string $value
     * @return CookieElementInterface
     * @throws InvalidArgumentException
     */
    public function make(string $name, string $value): CookieElementInterface;

    /**
     * Get all new cookie records from queue.
     * @return CookieElementInterface[]
     */
    public function getQueue(): array;
}
