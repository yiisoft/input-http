<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use Closure;
use ReflectionFunction;
use ReflectionParameter;
use Yiisoft\Hydrator\Context;

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
}
