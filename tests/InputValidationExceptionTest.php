<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Input\Http\InputValidationException;
use Yiisoft\Validator\Result;

final class InputValidationExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $result = new Result();

        $exception = new InputValidationException($result);

        $this->assertSame('Input validation error.', $exception->getMessage());
        $this->assertSame($result, $exception->getResult());
    }
}
