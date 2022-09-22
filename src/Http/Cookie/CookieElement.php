<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Cookie;

use InvalidArgumentException;

/**
 * A cookie element instance.
 */
class CookieElement implements CookieElementInterface
{
    /**
     * The name.
     * @var string
     */
    protected string $name = '';

    /**
     * The value be formatted.
     * @var string
     */
    protected string $value = '';

    /**
     * The expiration time.
     * @var string
     */
    protected string $expire = '';

    /**
     * The permanent.
     * @var bool
     */
    protected bool $permanent = false;

    /**
     * The domain.
     * @var string
     */
    protected string $domain = '';

    /**
     * The path.
     * @var string
     */
    protected string $path = '/';

    /**
     * The secure.
     * @var bool
     */
    protected bool $secure = false;

    /**
     * The httpOnly.
     * @var bool
     */
    protected bool $http_only = false;

    /**
     * The sameSite.
     * @var string
     */
    protected string $same_site = 'none';

    /**
     * @inheritDoc
     */
    public function __construct(string $name, string $value)
    {
        $name === "" &&
        throw new InvalidArgumentException("Invalid cookie name, which should not be empty.");

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function withExpire(int $time): static
    {
        $this->expire = date("l, d-M-Y H:i:s \G\M\T", time() + $time);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withPermanent(bool $permanent): static
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withSecure(bool $secure): static
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withHttpOnly(bool $httpOnly): static
    {
        $this->http_only = $httpOnly;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withSameSite(string $sameSite): static
    {
        $sameSite = strtolower($sameSite);

        !in_array($sameSite, ['none', 'lax', 'strict']) &&
        throw new InvalidArgumentException("Invalid cookie sameSite, which should be none, lax and strict.");

        $this->same_site = $sameSite;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $raw = "$this->name=$this->value";
        $raw .= $this->expire === "" ? '' : "; Expires:$this->expire";
        $raw .= $this->permanent === false ? '' : "; Max-Age:$this->expire";
        $raw .= $this->domain === "" ? '' : "; Domain:$this->domain";
        $raw .= $this->path === "" ? '' : "; Path:$this->path";
        $raw .= $this->secure === false ? '' : "; Secure";
        $raw .= $this->http_only === false ? '' : "; HttpOnly";
        $raw .= $this->same_site === "" ? '' : "; SameSite:$this->same_site";

        return $raw;
    }
}
