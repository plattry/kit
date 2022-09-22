<?php

declare(strict_types = 1);

namespace Plattry\Kit\Log\Driver;

use Throwable;

/**
 * Describe a logger driver instance.
 */
interface DriverInterface
{
    /**
     * Write log data to file or remote server.
     * @param array $data
     * @return void
     * @throws Throwable
     */
    public function writeBuffer(array $data): void;
}
