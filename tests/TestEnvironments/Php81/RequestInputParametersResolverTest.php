<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\TestEnvironments\Php81;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\Tests\Support\Input\PersonInput;
use Yiisoft\Input\Http\Tests\Support\TestHelper;

final class RequestInputParametersResolverTest extends TestCase
{
    public function testUnionType(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([]);

        $resolver = TestHelper::createRequestInputParametersResolver($request);
        $parameters = TestHelper::getParameters(static fn(PersonInput|string $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame([], $result);
    }
}
