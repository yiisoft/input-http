<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use ReflectionType;
use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCasterInterface;

final class CallableTypeCaster implements TypeCasterInterface
{
    public function __construct(
        private $callable,
    ) {
    }

    public function cast(mixed $value, ?ReflectionType $type): Result
    {
        return Result::success(($this->callable)($value));
    }
}
