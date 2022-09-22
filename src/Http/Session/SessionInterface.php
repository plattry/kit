<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Session;

use InvalidArgumentException;

/**
 * Describe a http session instance.
 */
interface SessionInterface
{
    /**
     * The constructor.
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver);

    /**
     * Get the session name.
     * @return string
     */
    public static function getName(): string;

    /**
     * Set a session name.
     * @param string $name
     * @return void
     * @throws InvalidArgumentException
     */
    public static function setName(string $name): void;

    /**
     * Get the session id.
     * @return false|string
     */
    public function getId(): false|string;

    /**
     * Set a session id.
     * @param string $id
     * @return void
     */
    public function setId(string $id): void;

    /**
     * Get the session expire time.
     * @return int
     */
    public function getExpire(): int;

    /**
     * Set a session expire time(seconds).
     * @param int $expire
     * @return void
     */
    public function setExpire(int $expire): void;

    /**
     * Check if a key exists.
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get the session value by key.
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed;

    /**
     * Set a session key && value.
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set(string $name, mixed $value): void;

    /**
     * Delete the session value by key.
     * @param string $name
     * @return void
     */
    public function del(string $name): void;

    /**
     * Save current session.
     * @return void
     */
    public function save(): void;

    /**
     * Destroy current session.
     * @return void
     */
    public function destroy(): void;
}
