<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunction;
use ReflectionParameter;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributeResolveContext;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\ObjectFactory\ContainerObjectFactory;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\Validator\Attribute\ValidateResolver;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;
use Yiisoft\Injector\Injector;
use Yiisoft\Input\Http\Attribute\Data\FromQueryResolver;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Validator\Validator;

final class TestHelper
{
    public static function createHydrator(?array $definitions = null): Hydrator
    {
        if ($definitions === null) {
            return new Hydrator();
        }

        $container = new SimpleContainer(
            $definitions,
            static fn(string $class) => new $class(),
        );

        return new Hydrator(
            attributeResolverFactory: new ContainerAttributeResolverFactory($container),
            objectFactory: new ContainerObjectFactory(
                new Injector($container),
            ),
        );
    }

    public static function createParameterAttributesHandler(array $definitions): ParameterAttributesHandler
    {
        return new ParameterAttributesHandler(
            new ContainerAttributeResolverFactory(
                new SimpleContainer($definitions),
            ),
            new Hydrator(),
        );
    }

    public static function createParameterAttributeResolveContext(): ParameterAttributeResolveContext
    {
        return new ParameterAttributeResolveContext(
            self::getParameters(static fn(int $a) => null)['a'],
            Result::fail(),
            new ArrayData(),
        );
    }

    /**
     * @psalm-return array<string,ReflectionParameter>
     */
    public static function getParameters(Closure $closure): array
    {
        $reflection = new ReflectionFunction($closure);

        $result = [];
        foreach ($reflection->getParameters() as $parameter) {
            $result[$parameter->getName()] = $parameter;
        }

        return $result;
    }

    public static function createRequestInputParametersResolver(
        ServerRequestInterface $request,
        bool $useValidatingHydrator = true,
        ?bool $throwInputValidationException = null,
    ): RequestInputParametersResolver {
        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $validator = new Validator();
        $validateResolver = new ValidateResolver($validator);
        $hydrator = self::createHydrator([
            ValidateResolver::class => $validateResolver,
            FromQueryResolver::class => new FromQueryResolver($requestProvider),
        ]);

        if ($useValidatingHydrator) {
            $hydrator = new ValidatingHydrator(
                $hydrator,
                $validator,
                $validateResolver,
            );
        }

        return $throwInputValidationException === null
            ? new RequestInputParametersResolver($hydrator)
            : new RequestInputParametersResolver($hydrator, $throwInputValidationException);
    }
}
