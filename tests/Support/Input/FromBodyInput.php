<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Input\Http\Attribute\Data\FromBody;

#[FromBody]
final class FromBodyInput
{
    public string $a = '';
    public string $b = '';
}
