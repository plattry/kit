<?php

declare(strict_types = 1);

namespace Plattry\Kit\Log;

use DateTime;
use Plattry\Kit\Log\Driver\DriverInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;
use Throwable;

/**
 * A logger instance.
 */
class Logger extends AbstractLogger
{
    /**
     * The log level.
     * @var string
     */
    protected string $level = LogLevel::DEBUG;

    /**
     * The allowed log levels.
     * @var array
     */
    protected array $allow = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY
    ];

    /**
     * The datetime format.
     * @var string
     */
    protected string $date = 'Y-m-d H:i:s.u';

    /**
     * The buffer size.
     * @var int
     */
    protected int $size = 0;

    /**
     * The log buffer.
     * @var array
     */
    protected array $buffer = [];

    /**
     * The logger driver instance.
     * @var DriverInterface
     */
    protected DriverInterface $driver;

    /**
     * The constructor.
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set a log level.
     * @param string $level
     * @return void
     */
    public function setLevel(string $level): void
    {
        $this->allow = match ($level) {
            LogLevel::DEBUG => [
                LogLevel::DEBUG,
                LogLevel::INFO,
                LogLevel::NOTICE,
                LogLevel::WARNING,
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::INFO => [
                LogLevel::INFO,
                LogLevel::NOTICE,
                LogLevel::WARNING,
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::NOTICE => [
                LogLevel::NOTICE,
                LogLevel::WARNING,
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::WARNING => [
                LogLevel::WARNING,
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::ERROR => [
                LogLevel::ERROR,
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::CRITICAL => [
                LogLevel::CRITICAL,
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::ALERT => [
                LogLevel::ALERT,
                LogLevel::EMERGENCY
            ],
            LogLevel::EMERGENCY => [
                LogLevel::EMERGENCY
            ]
        };

        $this->level = $level;
    }

    /**
     * Get the log level.
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * Set a date format, like function date().
     * @param string $format
     */
    public function setDate(string $format): void
    {
        $this->date = $format;
    }

    /**
     * Get the date format.
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Set a buffer size.
     * @param int $size
     * @return void
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Get the buffer size.
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Add a message to buffer.
     * @param string $level
     * @param string $message
     * @param array $context
     * @throws Throwable
     */
    protected function addBuffer(string $level, string $message, array $context)
    {
        $record = ['dt' => '', 'lv' => $level, 'msg' => '', 'ts' => 0];

        try {
            $datetime = new DateTime('now');
            $record['dt'] = $datetime->format($this->date);
            $record['ts'] = $datetime->getTimestamp();
        } catch (Throwable) {
            $timestamp = time();
            $record['dt'] = date($this->date, $timestamp);
            $record['ts'] = $timestamp;
        } finally {
            $record['msg'] = str_replace(
                array_map(fn($key) => "%$key%", array_keys($context)),
                array_values($context),
                $message
            );
        }

        if (count($this->buffer) >= $this->size)
            $this->flushBuffer();

        $this->buffer[] = $record;
    }

    /**
     * Flush buffer to file or remote server by driver.
     * @return void
     * @throws Throwable
     */
    public function flushBuffer(): void
    {
        if (empty($this->buffer))
            return;

        $data = $this->buffer;
        $this->buffer = [];

        $this->driver->writeBuffer($data);
    }

    /**
     * Get the messages in buffer.
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * Get the log driver instance.
     * @return DriverInterface
     */
    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        in_array($level, $this->allow) && $this->addBuffer($level, (string) $message, $context);
    }

    /**
     * Logger destructor.
     * @throws Throwable
     */
    public function __destruct()
    {
        $this->flushBuffer();
    }
}
