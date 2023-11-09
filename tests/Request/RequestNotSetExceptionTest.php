<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Request;

use PHPUnit\Framework\TestCase;
use Yiisoft\Input\Http\Request\RequestNotSetException;

final class RequestNotSetExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $exception = new RequestNotSetException();

        $this->assertSame('Request is not set.', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
