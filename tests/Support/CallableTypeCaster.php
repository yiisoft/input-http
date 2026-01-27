<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support;

use Yiisoft\Hydrator\Result;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;

final class CallableTypeCaster implements TypeCasterInterface
{
    public function __construct(
        private $callable,
    ) {}

    public function cast(mixed $value, TypeCastContext $context): Result
    {
        return Result::success(($this->callable)($value));
    }
}
