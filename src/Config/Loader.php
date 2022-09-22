<?php

declare(strict_types=1);

namespace Plattry\Kit\Config;

use InvalidArgumentException;

/**
 * A config recourse loader instance.
 */
class Loader
{
    /**
     * Load config files.
     * @param string $resource
     * @param bool $recursive
     * @return array|string[]
     */
    public function load(string $resource, bool $recursive = false): array
    {
        if (is_dir($resource)) {
            return $this->scan($resource, $recursive);
        } elseif (is_file($resource)) {
            return [$resource];
        } else {
            throw new InvalidArgumentException("Invalid config resource, `$resource` should be an exist directory or file.");
        }
    }

    /**
     * Scan directory and get all files.
     * @param string $dirname
     * @param bool $recursive
     * @return array
     */
    protected function scan(string $dirname, bool $recursive = false): array
    {
        !is_dir($dirname) &&
        throw new InvalidArgumentException("Invalid path, `$dirname` should be an exist directory.");

        $files = [];
        foreach (scandir($dirname) as $filename) {
            if ('.' === $filename || '..' === $filename)
                continue;

            $fullName = $dirname . '/' . $filename;

            if (is_file($fullName))
                $files[] = $fullName;

            if (is_dir($fullName) && $recursive)
                array_push($files, ...static::scan($fullName, $recursive));
        }

        return $files;
    }
}
