<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Foundation;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A http message instance.
 */
class Message implements MessageInterface
{
    /**
     * The protocol version.
     * @var string
     */
    protected string $version = "1.1";

    /**
     * The header.
     * @var string[][]
     */
    protected array $headers = [];

    /**
     * The body stream.
     * @var StreamInterface
     */
    protected StreamInterface $body;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version): static
    {
        $this->version = (string)$version;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        return implode(';', $this->headers[strtolower($name)] ?? []);
    }

    /**
     * Return an instance with the provided value replacing the headers.
     * @param array $headers
     * @return static
     */
    public function withHeaders(array $headers): static
    {
        $this->headers = [];

        foreach ($headers as $name => $header)
            $this->withHeader($name, $header);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value): static
    {
        $name = strtolower($name);

        unset($this->headers[$name]);

        $this->withAddedHeader($name, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value): static
    {
        $name = strtolower($name);

        if (is_string($value))
            $value = "set-cookie" === $name ? [$value] : explode(";", $value);

        (!is_array($value) || empty($value)) &&
        throw new InvalidArgumentException("Invalid http header, the value should be a string and string[].");

        foreach ($value as $item)
            $this->headers[$name][] = $item;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name): static
    {
        unset($this->headers[strtolower($name)]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body): static
    {
        $this->body = $body;

        return $this;
    }
}
