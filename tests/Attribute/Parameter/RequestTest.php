<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Request;
use Yiisoft\Input\Http\Attribute\Parameter\RequestResolver;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\RequestProvider\RequestProviderInterface;

final class RequestTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
            'c' => 'three',
        ]);

        $input = new class {
            #[Request('a')]
            public string $a = '';
            #[Request('b')]
            public string $b = '';
            #[Request]
            public string $c = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
        $this->assertSame('three', $input->c);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator([]);

        $input = new class {
            #[Request('a')]
            public string $a = '';
            #[Request('b')]
            public string $b = '';
            #[Request]
            public string $c = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
        $this->assertSame('', $input->b);
        $this->assertSame('', $input->c);
    }

    public function testNonExistPath(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
        ]);

        $input = new class {
            #[Request('a.b')]
            public string $a = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
    }

    public function testNonExistPathReturnsFailResult(): void
    {
        $request = (new ServerRequest())->withAttribute('a', 'one');

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $resolver = new RequestResolver($requestProvider);

        $attribute = new Request('non-existing-key');
        $context = TestHelper::createParameterAttributeResolveContext();

        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertFalse($result->isResolved());
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new RequestResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new Body();
        $context = TestHelper::createParameterAttributeResolveContext();

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

        return TestHelper::createHydrator([
            RequestResolver::class => new RequestResolver($requestProvider),
        ]);
    }
}
