<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Input\Http\Attribute\Data\FromQuery;

#[FromQuery]
final class FromQueryInput
{
    public string $a = '';
    public string $b = '';
}
