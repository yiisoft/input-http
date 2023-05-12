<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Hydrator\Validator\ValidatedInputInterface;
use Yiisoft\Hydrator\Validator\ValidatedInputTrait;
use Yiisoft\Input\Http\Attribute\Data\FromQuery;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;

#[FromQuery]
final class PersonInput implements RequestInputInterface, ValidatedInputInterface
{
    use ValidatedInputTrait;

    public function __construct(
        #[Required]
        public string $name = '',
    ) {
    }
}
