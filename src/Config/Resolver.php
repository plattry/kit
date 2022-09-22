<?php

declare(strict_types=1);

namespace Plattry\Kit\Config;

use InvalidArgumentException;
use RuntimeException;

/**
 * A config options resolver instance.
 */
class Resolver
{
    /**
     * The supported config file types.
     * @var array|string[]
     */
    const SUPPORT_FILE_TYPE = ['php', 'ini', 'json'];

    /**
     * Parse config options.
     * @param string $filename
     * @return array
     */
    public function parse(string $filename): array
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        !in_array($ext, self::SUPPORT_FILE_TYPE) &&
        throw new InvalidArgumentException("Invalid config resource, supported config file type is .php, .ini and .json.");

        return match ($ext) {
            'php' => self::parsePhp($filename),
            'ini' => self::parseIni($filename),
            'json' => self::parseJson($filename)
        };
    }

    /**
     * Parse .php file.
     * @param string $filename
     * @return array
     */
    protected static function parsePhp(string $filename): array
    {
        return include $filename;
    }

    /**
     * Parse .ini file.
     * @param string $filename
     * @return array
     */
    protected static function parseIni(string $filename): array
    {
        ($content = parse_ini_file($filename, true)) === false &&
        throw new RuntimeException("An error occur while parsing `$filename`.");

        return $content;
    }

    /**
     * Parse .json file.
     * @param string $filename
     * @return array
     */
    protected static function parseJson(string $filename): array
    {
        ($content = file_get_contents($filename)) === false &&
        throw new RuntimeException("An error occur while reading `$filename`.");

        !is_array($data = json_decode($content, true)) &&
        throw new RuntimeException("An error occur while parsing `$filename`.");

        return $data;
    }
}
