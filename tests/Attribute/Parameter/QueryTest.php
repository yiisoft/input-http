<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Parameter;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Input\Http\Attribute\Parameter\QueryResolver;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class QueryTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
            'c' => 'three',
        ]);

        $input = new class () {
            #[Query('a')]
            public string $a = '';
            #[Query('b')]
            public string $b = '';
            #[Query]
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

        $input = new class () {
            #[Query('a')]
            public string $a = '';
            #[Query('b')]
            public string $b = '';
            #[Query]
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

        $input = new class () {
            #[Query('a.b')]
            public string $a = '';
        };

        $hydrator->hydrate($input);

        $this->assertSame('', $input->a);
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new QueryResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new Body();
        $context = TestHelper::createContext();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . Query::class . '", but "' . Body::class . '" given.');
        $resolver->getParameterValue($attribute, $context);
    }

    private function createHydrator(array $queryParams): Hydrator
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($queryParams);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        return new Hydrator(
            new SimpleContainer([
                QueryResolver::class => new QueryResolver($requestProvider),
            ]),
        );
    }
}
