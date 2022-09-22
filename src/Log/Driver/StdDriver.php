<?php

declare(strict_types = 1);

namespace Plattry\Kit\Log\Driver;

/**
 * A std driver instance.
 */
class StdDriver implements DriverInterface
{
    /**
     * @inheritDoc
     */
    public function writeBuffer(array $data): void
    {
        $content = array_reduce(
            $data,
            fn($front, $next) => $front . sprintf("%s|%s|%s\n", $next['dt'], $next['lv'], $next['msg'])
        );

        file_put_contents("php://stdout", $content);
    }
}
