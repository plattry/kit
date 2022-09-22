<?php

declare(strict_types = 1);

namespace Plattry\Kit\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * A not-found exception instance.
 */
class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
