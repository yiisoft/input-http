<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use ReflectionType;
use Yiisoft\Hydrator\TypeCasterInterface;
use Yiisoft\Hydrator\Value;

final class CallableTypeCaster implements TypeCasterInterface
{
    public function __construct(
        private $callable,
    ) {
    }

    public function cast(mixed $value, ?ReflectionType $type): Value
    {
        return Value::success(($this->callable)($value));
    }
}
