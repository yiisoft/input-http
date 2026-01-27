<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use HttpSoft\Message\Request;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Message\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\BodyResolver;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\RequestProvider\RequestProviderInterface;

final class BodyTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
            'c' => 'three',
        ]);

        $input = new class {
            #[Body('a')]
            public string $a = '';
            #[Body('b')]
            public string $b = '';
            #[Body]
            public string $c = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
        $this->assertSame('three', $input->c);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator(null);

        $input = new class {
            #[Body('a')]
            public string $a = '';
            #[Body('b')]
            public string $b = '';
            #[Body]
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
            #[Body('a.b')]
            public string $a = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
    }

    public function testNonExistPathReturnsFailResult(): void
    {
        $request = new ServerRequest(parsedBody: ['a' => 'one']);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $resolver = new BodyResolver($requestProvider);

        $attribute = new Body('non-existing-key');
        $context = TestHelper::createParameterAttributeResolveContext();

        $result = $resolver->getParameterValue($attribute, $context);

        $this->assertFalse($result->isResolved());
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new BodyResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new Query();
        $context = TestHelper::createParameterAttributeResolveContext();

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

        return TestHelper::createHydrator([
            BodyResolver::class => new BodyResolver($requestProvider),
        ]);
    }
}
