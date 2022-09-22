<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Foundation;

use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * A http factory instance.
 */
class HttpFactory implements
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return (new Request())
            ->withMethod($method)
            ->withUri($uri)
            ->withBody($this->createStream());
    }

    /**
     * @inheritDoc
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return (new Response())
            ->withStatus($code, $reasonPhrase)
            ->withBody($this->createStream());
    }

    /**
     * @inheritDoc
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        !$uri instanceof UriInterface && ($uri = $this->createUri($uri));

        return (new ServerRequest($serverParams))
            ->withMethod($method)
            ->withUri($uri)
            ->withRequestTarget($uri->getPath())
            ->withBody($this->createStream());
    }

    /**
     * @inheritDoc
     */
    public function createStream(string $content = ''): StreamInterface
    {
        return new Stream($content);
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new Stream(file_get_contents($filename));
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream(strval(stream_get_contents($resource)));
    }

    /**
     * @inheritDoc
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        return new UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    /**
     * @inheritDoc
     */
    public function createUri(string $uri = ''): UriInterface
    {
        ($info = parse_url($uri)) === false &&
        throw new InvalidArgumentException("Invalid uri, `$uri` is a seriously malformed uri.");

        return (new Uri())->withScheme($info['scheme'] ?? "")
            ->withUserInfo($info['user'] ?? "", $info['pass'] ?? null)
            ->withHost($info['host'] ?? "")
            ->withPort(intval($info['port'] ?? ""))
            ->withPath($info['path'] ?? "")
            ->withQuery($info['query'] ?? "")
            ->withFragment($info['fragment'] ?? "");
    }
}
