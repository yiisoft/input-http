<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunction;
use ReflectionParameter;
use Yiisoft\Hydrator\Context;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Validator\Attribute\ValidateResolver;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;
use Yiisoft\Input\Http\Attribute\Data\FromQueryResolver;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Validator\Validator;

final class TestHelper
{
    public static function createContext(): Context
    {
        return new Context(self::getParameters(static fn(int $a) => null)['a'], false, null, [], []);
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
    ): RequestInputParametersResolver {
        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $validator = new Validator();
        $validateResolver = new ValidateResolver($validator);
        $container = new SimpleContainer(
            [
                ValidateResolver::class => $validateResolver,
                FromQueryResolver::class => new FromQueryResolver($requestProvider),
            ],
        );

        if ($useValidatingHydrator) {
            $hydrator = new ValidatingHydrator(
                new Hydrator($container),
                $validator,
                $validateResolver,
            );
        } else {
            $hydrator = new Hydrator($container);
        }

        return new RequestInputParametersResolver($hydrator);
    }
}
