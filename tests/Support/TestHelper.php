<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use Closure;
use ReflectionFunction;
use ReflectionParameter;

final class TestHelper
{
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
