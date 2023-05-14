<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Request\Catcher;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Input\Http\Request\Catcher\RequestCatcherMiddleware;
use Yiisoft\Input\Http\Request\RequestProvider;

final class RequestCatcherMiddlewareTest extends TestCase
{
    public function testBase(): void
    {
        $requestProvider = new RequestProvider();
        $middleware = new RequestCatcherMiddleware($requestProvider);
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);

        $middleware->process($request, $handler);

        $this->assertSame($request, $requestProvider->get());
    }
}
