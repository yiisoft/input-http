<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\ParameterAttributesHandler;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

final class HydratorAttributeParametersResolver implements ParametersResolverInterface
{
    private ParameterAttributesHandler $handler;

    public function __construct(
        ContainerInterface $container,
        ?TypeCasterInterface $typeCaster = null,
    ) {
        $this->handler = new ParameterAttributesHandler($container, $typeCaster ?? new SimpleTypeCaster());
    }

    public function resolve(array $parameters, ServerRequestInterface $request): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            $handleResult = $this->handler->handle($parameter);
            if ($handleResult->isResolved()) {
                $result[$parameter->getName()] = $handleResult->getValue();
            }
        }

        return $result;
    }
}
