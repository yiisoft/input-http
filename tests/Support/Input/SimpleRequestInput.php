<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Input\Http\Attribute\Data\FromQuery;
use Yiisoft\Input\Http\RequestInputInterface;

#[FromQuery]
final class SimpleRequestInput implements RequestInputInterface
{
    public string $a = '';
    public string $b = '';
}
