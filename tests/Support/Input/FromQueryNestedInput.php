<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Input\Http\Attribute\Data\FromQuery;

#[FromQuery('MyForm.1')]
final class FromQueryNestedInput
{
    public string $a = '';
    public string $b = '';
}
