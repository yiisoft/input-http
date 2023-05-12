<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Validator\ValidatedInputInterface;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

final class RequestInputParametersResolver implements ParametersResolverInterface
{
    public function __construct(
        private HydratorInterface $hydrator,
        private bool $throwInputValidationException = false,
    ) {
    }

    /**
     * @param ReflectionParameter[] $parameters
     *
     * @throws InputValidationException
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
                $value = $this->hydrator->create($class);
                $this->processValidation($value);
                $result[$parameter->getName()] = $value;
            }
        }
        return $result;
    }

    /**
     * @throws InputValidationException
     */
    private function processValidation(mixed $value): void
    {
        if (
            !$this->throwInputValidationException
            || !$value instanceof ValidatedInputInterface
        ) {
            return;
        }

        $result = $value->getValidationResult();
        if ($result !== null && !$result->isValid()) {
            throw new InputValidationException($result);
        }
    }
}
