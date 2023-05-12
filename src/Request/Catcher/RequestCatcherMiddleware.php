<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request\Catcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Input\Http\Request\RequestProviderInterface;

final class RequestCatcherMiddleware implements MiddlewareInterface
{
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
