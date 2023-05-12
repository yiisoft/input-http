<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributesHandler;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

final class InputAttributeParametersResolver implements ParametersResolverInterface
{
    public function __construct(
        private ParameterAttributesHandler $handler,
    ) {
    }

    public function resolve(array $parameters, ServerRequestInterface $request): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            try {
                $value = $this->handler->handle($parameter);
            } catch (NotResolvedException) {
                continue;
            }

            $result[$parameter->getName()] = $value;
        }

        return $result;
    }
}
