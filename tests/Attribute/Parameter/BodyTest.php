<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\BodyResolver;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
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

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new BodyResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new Query();
        $context = TestHelper::createContext();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Body::class . '", but "' . Query::class . '" given.');
        $resolver->getParameterValue($attribute, $context);
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