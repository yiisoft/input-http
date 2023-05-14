<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Input\Http\Attribute\Data\FromBody;

#[FromBody('MyForm.1')]
final class FromBodyNestedInput
{
    public string $a = '';
    public string $b = '';
}
