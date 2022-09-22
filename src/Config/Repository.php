<?php

declare(strict_types=1);

namespace Plattry\Kit\Config;

use InvalidArgumentException;
use RuntimeException;

/**
 * A config repository instance.
 */
class Repository implements RepositoryInterface
{
    /**
     * The config set.
     * @var array
     */
    protected array $set = [];

    /**
     * The config loader instance.
     * @var Loader
     */
    protected Loader $loader;

    /**
     * The config resolver instance.
     * @var Resolver
     */
    protected Resolver $resolver;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->loader = new Loader();
        $this->resolver = new Resolver();
    }

    /**
     * @inheritDoc
     */
    public function import(string|array|callable $resource, bool $recursive = false): void
    {
        if (is_callable($resource)) {
            $option = call_user_func($resource);
            if (is_array($option)) {
                $this->merge($option);

                return;
            }

            throw new InvalidArgumentException("Invalid config resource, callback should return a config option array.");
        }

        foreach ((is_string($resource) ? [$resource] : $resource) as $item) {
            foreach ($this->loader->load($item, $recursive) as $file) {
                $this->merge($this->resolver->parse($file));
            }
        }
    }

    /**
     * Merge a config option.
     * @param array $option
     * @return void
     */
    protected function merge(array $option): void
    {
        $this->set = array_merge($this->set, $option);
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        $set = $this->set;

        while ($point = strpos($name, '.')) {
            $set = $set[substr($name, 0, $point)] ?? false;
            if ($set === false)
                return false;

            $name = substr($name, $point + 1);
        }

        return !($point === 0) && isset($set[$name]);
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): mixed
    {
        $path = $name;
        $set = $this->set;

        do {
            $point = strpos($path, '.');
            $first = $point === false ? $path : substr($path, 0, $point);
            $set = $set[$first] ?? false;
            if ($set === false)
                break;
            if ($point === false)
                return $set;

            $path = substr($path, $point + 1);
        } while (true);

        throw new RuntimeException("Not found config option `$name`.");
    }
}
