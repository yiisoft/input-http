<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request\Catcher;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

/**
 * Stores request into {@see RequestProviderInterface}.
 * You need to add this into your middleware parameters resolver in {@see MiddlewareFactory}. Usually used as additional
 * parameters resolver in {@see CompositeParametersResolver}.
 */
final class RequestCatcherParametersResolver implements ParametersResolverInterface
{
    /**
     * @param RequestProviderInterface $provider The request provider.
     */
    public function __construct(
        private RequestProviderInterface $provider,
    ) {
    }

    public function resolve(array $parameters, ServerRequestInterface $request): array
    {
        $this->provider->set($request);
        return [];
    }
}
