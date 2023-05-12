<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Request\Catcher;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\Request\Catcher\RequestCatcherParametersResolver;
use Yiisoft\Input\Http\Request\RequestProvider;

final class RequestCatcherParametersResolverTest extends TestCase
{
    public function testBase(): void
    {
        $requestProvider = new RequestProvider();
        $parametersResolver = new RequestCatcherParametersResolver($requestProvider);
        $request = $this->createMock(ServerRequestInterface::class);

        $parametersResolver->resolve([], $request);

        $this->assertSame($request, $requestProvider->get());
    }
}
