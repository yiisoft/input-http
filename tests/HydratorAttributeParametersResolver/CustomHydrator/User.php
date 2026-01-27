<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\HydratorAttributeParametersResolver\CustomHydrator;

final class User
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
