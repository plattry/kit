<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Foundation;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * A http server request instance.
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * The customer attributes.
     * @var array
     */
    protected array $attributes = [];

    /**
     * The query params.
     * @var array
     */
    protected array $query = [];

    /**
     * The parsed body params.
     * @var array|object|null
     */
    protected array|object|null $request = [];

    /**
     * The cookie params.
     * @var array
     */
    protected array $cookies = [];

    /**
     * The uploaded files.
     * @var UploadedFileInterface[]
     */
    protected array $files = [];

    /**
     * The server params.
     * @var array
     */
    protected array $server = [];

    /**
     * The constructor.
     * @param array $server
     */
    public function __construct(array $server)
    {
        $this->server = $server;
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies): static
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->files;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploadedFiles): static
    {
        foreach ($uploadedFiles as $uploadedFile) {
            !$uploadedFile instanceof UploadedFileInterface &&
            throw new InvalidArgumentException("Invalid uploaded file, which must be the instances of UploadedFileInterface.");
        }

        $this->files = $uploadedFiles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody(): array|object|null
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data): static
    {
        $this->request = $data;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute($name, $value): static
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute($name): static
    {
        unset($this->attributes[$name]);

        return $this;
    }
}
