<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionParameter;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCaster\PhpNativeTypeCaster;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

final class HydratorAttributeParametersResolver implements ParametersResolverInterface
{
    private TypeCasterInterface $typeCaster;
    private HydratorInterface $hydrator;

    public function __construct(
        private ParameterAttributesHandler $handler,
        ?TypeCasterInterface $typeCaster = null,
        ?HydratorInterface $hydrator = null,
    ) {
        $this->typeCaster = $typeCaster ?? new PhpNativeTypeCaster();
        $this->hydrator = $hydrator ?? new Hydrator(typeCaster: $this->typeCaster);
    }

    public function resolve(array $parameters, ServerRequestInterface $request): array
    {
        $result = [];

        foreach ($parameters as $parameter) {
            $handleResult = $this->handler->handle($parameter);
            if ($handleResult->isResolved()) {
                $result[$parameter->getName()] = $this->prepareValue($handleResult, $parameter);
            }
        }

        return $result;
    }

    public function prepareValue(Result $handleResult, ReflectionParameter $parameter): mixed
    {
        $value = $handleResult->getValue();

        $typeCastResult = $this->typeCaster->cast(
            $value,
            new TypeCastContext($this->hydrator, $parameter)
        );

        return $typeCastResult->isResolved() ? $typeCastResult->getValue() : $value;
    }
}
