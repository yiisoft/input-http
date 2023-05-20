<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\NotResolvedException;
use Yiisoft\Hydrator\ParameterAttributesHandler;
use Yiisoft\Hydrator\TypeCaster\SimpleTypeCaster;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

/**
 * HydratorAttributeParametersResolver resolves parameters using attribute handlers.
 */
final class HydratorAttributeParametersResolver implements ParametersResolverInterface
{
    /**
     * @var ParameterAttributesHandler The attribute handler.
     */
    private ParameterAttributesHandler $handler;

    /**
     * @param ContainerInterface $container The container to get resolver from.
     * @param TypeCasterInterface|null $typeCaster The type caster to use.
     */
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
