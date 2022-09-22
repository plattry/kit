<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Session;

use RuntimeException;

/**
 * A file driver instance.
 */
class FileDriver implements DriverInterface
{
    /**
     * The base directory.
     * @var string
     */
    protected static string $path = '/var/plattry/session';

    /**
     * The file prefix.
     * @var string
     */
    protected static string $prefix = 'plattry_session';

    /**
     * Get the storage path.
     * @return string
     */
    public static function getPath(): string
    {
        return self::$path;
    }

    /**
     * Set a storage path.
     * @param string $path
     * @return void
     */
    public static function setPath(string $path): void
    {
        !is_dir($path) && !mkdir($path, 0777, true) &&
        throw new RuntimeException("An error occurred while making $path");

        self::$path = $path;
    }

    /**
     * Get the file prefix.
     * @return string
     */
    public static function getPrefix(): string
    {
        return self::$prefix;
    }

    /**
     * Set a file prefix.
     * @param string $prefix
     * @return void
     */
    public static function setPrefix(string $prefix): void
    {
        self::$prefix = $prefix;
    }

    /**
     * Get the session filename.
     * @param string $id
     * @return string
     */
    protected static function getKey(string $id): string
    {
        return sprintf("%s/%s_%s", static::$path, static::$prefix, $id);
    }

    /**
     * @inheritDoc
     */
    public function read(string $id): array
    {
        $filename = self::getKey($id);
        if (!file_exists($filename))
            return ["", []];

        $content = file_get_contents($filename);
        if ($content === false)
            return ["", []];

        [$data, $validTime] = unserialize($content);
        if ($validTime < time()) {
            unlink($filename);
            return ["", []];
        }

        return [$id, $data];
    }

    /**
     * @inheritDoc
     */
    public function write(string $id, array $data, int $expire): void
    {
        $filename = self::getKey($id);
        $mkTime = file_exists($filename) ? (filectime($filename) ?: time()) : time();
        $content = serialize([$data, $mkTime + $expire]);

        file_put_contents($filename, $content);
    }

    /**
     * @inheritDoc
     */
    public function destroy(string $id): void
    {
        $filename = self::getKey($id);
        file_exists($filename) && unlink($filename);
    }
}
