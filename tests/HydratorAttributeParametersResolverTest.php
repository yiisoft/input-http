<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\ParameterAttributesHandler;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\BodyResolver;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Input\Http\Attribute\Parameter\QueryResolver;
use Yiisoft\Input\Http\HydratorAttributeParametersResolver;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class HydratorAttributeParametersResolverTest extends TestCase
{
    public function testBase(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['a' => 1]);
        $request->method('getQueryParams')->willReturn(['b' => 2]);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $resolver = new HydratorAttributeParametersResolver(
            new ParameterAttributesHandler(
                new SimpleContainer([
                    QueryResolver::class => new QueryResolver($requestProvider),
                    BodyResolver::class => new BodyResolver($requestProvider),
                ]),
            ),
        );

        $parameters = TestHelper::getParameters(
            static fn(
                #[Body('a')]
                int $a,
                #[Query('b')]
                int $b,
                #[Query('non-exist')]
                int $c,
            ) => null,
        );

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['a', 'b'], array_keys($result));
        $this->assertSame(1, $result['a']);
        $this->assertSame(2, $result['b']);
    }
}
