<?php

declare(strict_types = 1);

namespace Plattry\Kit\Log\Driver;

/**
 * A file driver instance.
 */
class FileDriver implements DriverInterface
{
    /**
     * The Split mode hour.
     * @var int
     */
    public const SPLIT_HOUR = 0;

    /**
     * The Split mode day.
     * @var int
     */
    public const SPLIT_DAY = 1;

    /**
     * The Split mode month.
     * @var int
     */
    public const SPLIT_MONTH = 1 << 1;

    /**
     * The base directory.
     * @var string
     */
    protected string $path = '/var/plattry/log';

    /**
     * The file split mode.
     * @var int
     */
    protected int $split = self::SPLIT_DAY;

    /**
     * Set a base directory of log files.
     * @param string $path
     */
    public function setPath(string $path): void
    {
        !is_dir($path) && !mkdir($path, 0777, true) &&
        throw new \RuntimeException("An error occurred while making $path.");

        $this->path = $path;
    }

    /**
     * Get the base directory of log files.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set a file split mode.
     * @param int $split
     */
    public function setSplit(int $split): void
    {
        $this->split = $split;
    }

    /**
     * Get the file split mode.
     * @return int
     */
    public function getSplit(): int
    {
        return $this->split;
    }

    /**
     * Generate a log file name.
     * @param string $path
     * @param int $split
     * @param int $timestamp
     * @return string
     */
    protected static function getFilename(string $path, int $split, int $timestamp): string
    {
        return match ($split) {
            self::SPLIT_HOUR => sprintf("%s/%s.log", $path, date('YmdH', $timestamp)),
            self::SPLIT_DAY => sprintf("%s/%s.log", $path, date('Ymd', $timestamp)),
            self::SPLIT_MONTH => sprintf("%s/%s.log", $path, date('Ym', $timestamp))
        };
    }

    /**
     * @inheritDoc
     */
    public function writeBuffer(array $data): void
    {
        $list = [];
        foreach ($data as $record) {
            $filename = self::getFilename($this->path, $this->split, $record['ts']);
            $list[$filename] = ($list[$filename] ?? '') .
                sprintf("%s|%s|%s\n", $record['dt'], $record['lv'], $record['msg']);
        }

        foreach ($list as $filename => $content) {
            file_put_contents($filename, $content, FILE_APPEND);
        }
    }
}
