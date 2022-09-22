<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * A generic container exception instance.
 */
class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
