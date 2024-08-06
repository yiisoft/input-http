<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Support\Input;

use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromQuery;
use Yiisoft\Validator\Rule\Required;

#[FromQuery]
final class PersonInput extends AbstractInput
{
    public function __construct(
        #[Required(message: 'Name is required.')]
        public string $name = '',
    ) {
    }
}
