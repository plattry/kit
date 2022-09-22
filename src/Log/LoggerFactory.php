<?php

declare(strict_types = 1);

namespace Plattry\Kit\Log;

use Plattry\Kit\Log\Driver\FileDriver;
use Plattry\Kit\Log\Driver\StdDriver;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * A logger factory instance.
 */
Class LoggerFactory
{
    /**
     * Create a logger with file driver.
     * @param string $level
     * @param string $date
     * @param int $size
     * @param string $path
     * @param int $split
     * @return LoggerInterface
     */
    public function createFileLogger(
        string $level = LogLevel::INFO,
        string $date = 'Y-m-d H:i:s.u',
        int $size = 50,
        string $path = "/var/plattry/log",
        int $split = FileDriver::SPLIT_DAY
    ): LoggerInterface
    {
        $driver = new FileDriver();
        $driver->setPath($path);
        $driver->setSplit($split);

        $logger = new Logger($driver);
        $logger->setLevel($level);
        $logger->setDate($date);
        $logger->setSize($size);

        return $logger;
    }

    /**
     * Create a logger with stdout driver.
     * @param string $level
     * @param string $date
     * @param int $size
     * @return Logger
     */
    public function createStdLogger(
        string $level = LogLevel::INFO,
        string $date = 'Y-m-d H:i:s.u',
        int $size = 0
    ): LoggerInterface
    {
        $driver = new StdDriver();

        $logger = new Logger($driver);
        $logger->setLevel($level);
        $logger->setDate($date);
        $logger->setSize($size);

        return $logger;
    }
}
