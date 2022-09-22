<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Cookie;

/**
 * A http cookie instance.
 */
class Cookie implements CookieInterface
{
    /**
     * The new cookies.
     * @var CookieElementInterface[]
     */
    protected array $queue = [];

    /**
     * @inheritDoc
     */
    public function make(string $name, string $value): CookieElementInterface
    {
        return $this->queue[] = new CookieElement($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function getQueue(): array
    {
        return $this->queue;
    }
}
