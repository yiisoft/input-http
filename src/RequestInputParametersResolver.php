<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

final class RequestInputParametersResolver implements ParametersResolverInterface
{
    public function __construct(
        private HydratorInterface $hydrator,
    ) {
    }

    /**
     * @param ReflectionParameter[] $parameters
     *
     * @return RequestInputInterface[]
     *
     * @psalm-param array<string,ReflectionParameter> $parameters
     * @psalm-return array<string,RequestInputInterface>
     */
    public function resolve(array $parameters, ServerRequestInterface $request): array
    {
        $result = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            $class = $type->getName();

            $reflectionClass = new ReflectionClass($class);
            if (
                $reflectionClass->isInstantiable()
                && $reflectionClass->implementsInterface(RequestInputInterface::class)
            ) {
                /** @psalm-var class-string<RequestInputInterface> $class */
                $result[$parameter->getName()] = $this->hydrator->create($class);
            }
        }
        return $result;
    }
}
