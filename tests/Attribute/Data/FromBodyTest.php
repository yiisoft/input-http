<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Data;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\Data;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\UnexpectedAttributeException;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Input\Http\Attribute\Data\FromBodyResolver;
use Yiisoft\Input\Http\Attribute\Data\FromQuery;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Input\Http\Tests\Support\Input\FromBodyInput;
use Yiisoft\Input\Http\Tests\Support\Input\FromBodyNestedInput;
use Yiisoft\Test\Support\Container\SimpleContainer;

final class FromBodyTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
        ]);

        $input = $hydrator->create(FromBodyInput::class);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator(null);

        $input = $hydrator->create(FromBodyInput::class);

        $this->assertSame('', $input->a);
        $this->assertSame('', $input->b);
    }

    public function testNestedArray(): void
    {
        $hydrator = $this->createHydrator([
            'MyForm' => [
                1 => [
                    'a' => 'one',
                    'b' => 'two',
                ],
            ],
        ]);

        $input = $hydrator->create(FromBodyNestedInput::class);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new FromBodyResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new FromQuery();
        $data = new Data();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . FromBody::class . '", but "' . FromQuery::class . '" given.');
        $resolver->prepareData($attribute, $data);
    }

    private function createHydrator(mixed $parsedBody): Hydrator
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn($parsedBody);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        return new Hydrator(
            new SimpleContainer([
                FromBodyResolver::class => new FromBodyResolver($requestProvider),
            ]),
        );
    }
}
