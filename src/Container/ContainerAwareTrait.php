<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container;

use Psr\Container\ContainerInterface;

/**
 * A container-aware instance.
 */
trait ContainerAwareTrait
{
    /**
     * The container instance.
     * @var ContainerInterface|null
     */
    protected ContainerInterface|null $container = null;

    /**
     * Set a container.
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface|null $container = null): void
    {
        $this->container = $container;
    }
}
