<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Session;

/**
 * Describe a http session driver instance.
 */
interface DriverInterface
{
    /**
     * Read the session data.
     * @param string $id
     * @return array
     */
    public function read(string $id): array;

    /**
     * Save the session data.
     * @param string $id
     * @param array $data
     * @param int $expire
     * @return void
     */
    public function write(string $id, array $data, int $expire): void;

    /**
     * Destroy the session data.
     * @param string $id
     * @return void
     */
    public function destroy(string $id): void;
}
