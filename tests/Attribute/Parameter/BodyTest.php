<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\BodyResolver;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class BodyTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
        ]);

        $input = new class () {
            #[Body('a')]
            public string $a = '';
            #[Body('b')]
            public string $b = '';
            #[Body]
            public array $all = [];
        };

        $hydrator->hydrate($input);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
        $this->assertSame(['a' => 'one', 'b' => 'two'], $input->all);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator(null);

        $input = new class () {
            #[Body('a')]
            public string $a = '';
            #[Body('b')]
            public string $b = '';
            #[Body]
            public array $all = [];
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
        $this->assertSame('', $input->b);
        $this->assertSame([], $input->all);
    }

    public function testNonExistPath(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
        ]);

        $input = new class () {
            #[Body('a.b')]
            public string $a = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
    }

    private function createHydrator(mixed $parsedBody): Hydrator
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($parsedBody);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        return new Hydrator(
            new SimpleContainer([
                BodyResolver::class => new BodyResolver($requestProvider),
            ]),
        );
    }
}
