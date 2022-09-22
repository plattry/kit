<?php

declare(strict_types = 1);

namespace Plattry\Kit\Http\Cookie;

use Plattry\Kit\Http\Processor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A cookie processor instance.
 */
class CookieProcessor extends Processor
{
    /**
     * The cookie instance.
     * @var CookieInterface
     */
    protected CookieInterface $cookie;

    /**
     * The constructor.
     * @param CookieInterface $cookie
     */
    public function __construct(CookieInterface $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * @inheritDoc
     */
    protected function after(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $values = array_map(fn($cookieElement) => (string)$cookieElement, $this->cookie->getQueue());
        if (!empty($values))
            $response->withAddedHeader('set-cookie', $values);

        return parent::after($request, $response);
    }
}
