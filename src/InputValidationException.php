<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http;

use RuntimeException;
use Yiisoft\Validator\Result;

final class InputValidationException extends RuntimeException
{
    public function __construct(
        private Result $result,
    ) {
        parent::__construct('Input validation error.');
    }

    public function getResult(): Result
    {
        return $this->result;
    }
}
