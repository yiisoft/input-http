<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request\Catcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

/**
 * Stores request into {@see RequestProviderInterface}.
 * You need to add this into your application middleware stack.
 */
final class RequestCatcherMiddleware implements MiddlewareInterface
{
    /**
     * @param RequestProviderInterface $provider The request provider.
     */
    public function __construct(
        private RequestProviderInterface $provider,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->provider->set($request);
        return $handler->handle($request);
    }
}
