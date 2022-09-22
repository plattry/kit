<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * A http request middle-processor instance.
 */
class Processor implements MiddlewareInterface
{
    /**
     * The action before stepping into the next processor.
     * @param ServerRequestInterface $request
     * @return ResponseInterface|null
     */
    protected function before(ServerRequestInterface $request): ResponseInterface|null
    {
        return null;
    }

    /**
     * The action after stepping out of the next processor.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function after(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->before($request);

        if (is_null($response)) {
            $response = $handler->handle($request);
        }

        return $this->after($request, $response);
    }
}
