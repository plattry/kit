<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Foundation;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * A http request instance.
 */
class Request extends Message implements RequestInterface
{
    /**
     * The method.
     * @var string
     */
    protected string $method = "GET";

    /**
     * The target.
     * @var string
     */
    protected string $target = "/";
    
    /**
     * The uri instance.
     * @var UriInterface
     */
    protected UriInterface $uri;

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        if ($this->target)
            return $this->target;

        if ($this->uri)
            return (string)$this->uri;

        return "/";
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($requestTarget): static
    {
        $this->target = $requestTarget;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method): static
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): static
    {
        $this->uri = $uri;

        return $this;
    }
}
