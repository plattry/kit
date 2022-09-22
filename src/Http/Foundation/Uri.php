<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Foundation;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * A http uri instance.
 */
class Uri implements UriInterface
{
    /**
     * The scheme.
     * @var string
     */
    protected string $scheme;

    /**
     * The user.
     * @var string
     */
    protected string $user;

    /**
     * The password.
     * @var string|null
     */
    protected string|null $pass;

    /**
     * The host.
     * @var string
     */
    protected string $host;

    /**
     * The port.
     * @var int|null
     */
    protected int|null $port;

    /**
     * The path.
     * @var string
     */
    protected string $path;

    /**
     * The query.
     * @var string
     */
    protected string $query;

    /**
     * The fragment.
     * @var string
     */
    protected string $fragment;

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function withScheme($scheme): static
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        $userinfo = $this->getUserInfo();

        return sprintf(
            "%s%s%s",
            $userinfo ? "$userinfo@" : "",
            $this->host,
            $this->port ? ":$this->port" : ""
        );
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return sprintf(
            "%s%s",
            $this->user,
            $this->pass ? ":$this->pass" : ""
        );
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = null): static
    {
        $this->user = $user;
        $this->pass = $password;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function withHost($host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int|null
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): static
    {
        if (is_null($port) || (is_int($port) && $port >= 0 && $port <= 65535)) {
            $this->port = $port;

            return $this;
        }

        throw new InvalidArgumentException("Invalid uri port, which should be int(0 ~ 65535) and null.");
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withFragment($fragment): static
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        $authority = $this->getAuthority();

        return sprintf(
            "%s:%s%s%s%s",
            $this->scheme,
            $authority ? "//$authority" : "",
            $this->path,
            $this->query ? "?$this->query" : "",
            $this->fragment ? "#$this->fragment" : ""
        );
    }
}