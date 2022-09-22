<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container;

use Psr\Container\ContainerInterface;

/**
 * Describe a maker instance.
 */
interface MakerInterface
{
    /**
     * The constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * Make a new object.
     * @param string|object $resource
     * @param array $vars
     * @return object
     */
    public function make(string|object $resource, array $vars = []): object;
}
