<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Request;
use Yiisoft\Input\Http\Attribute\Parameter\RequestResolver;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class RequestTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
        ]);

        $input = new class () {
            #[Request('a')]
            public string $a = '';
            #[Request('b')]
            public string $b = '';
            #[Request]
            public array $all = [];
        };

        $hydrator->hydrate($input);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
        $this->assertSame(['a' => 'one', 'b' => 'two'], $input->all);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator([]);

        $input = new class () {
            #[Request('a')]
            public string $a = '';
            #[Request('b')]
            public string $b = '';
            #[Request]
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
            #[Request('a.b')]
            public string $a = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new RequestResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new Body();
        $context = TestHelper::createContext();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Request::class . '", but "' . Body::class . '" given.');
        $resolver->getParameterValue($attribute, $context);
    }

    private function createHydrator(array $attributes): Hydrator
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttributes')->willReturn($attributes);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        return new Hydrator(
            new SimpleContainer([
                RequestResolver::class => new RequestResolver($requestProvider),
            ]),
        );
    }
}
