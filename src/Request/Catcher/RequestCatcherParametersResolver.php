<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Request\Catcher;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

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
