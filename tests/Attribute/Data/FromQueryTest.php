<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\Attribute\Data;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\ArrayData;
use Yiisoft\Hydrator\AttributeHandling\Exception\UnexpectedAttributeException;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Input\Http\Attribute\Data\FromQuery;
use Yiisoft\Input\Http\Attribute\Data\FromQueryResolver;
use Yiisoft\Input\Http\Tests\Support\Input\FromQueryInput;
use Yiisoft\Input\Http\Tests\Support\Input\FromQueryNestedInput;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\RequestProvider\RequestProviderInterface;

final class FromQueryTest extends TestCase
{
    public function testBase(): void
    {
        $hydrator = $this->createHydrator([
            'a' => 'one',
            'b' => 'two',
        ]);

        $input = $hydrator->create(FromQueryInput::class);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
    }

    public function testWithoutBody(): void
    {
        $hydrator = $this->createHydrator([]);

        $input = $hydrator->create(FromQueryInput::class);

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

        $input = $hydrator->create(FromQueryNestedInput::class);

        $this->assertSame('one', $input->a);
        $this->assertSame('two', $input->b);
    }

    public function testUnexpectedAttributeException(): void
    {
        $resolver = new FromQueryResolver($this->createMock(RequestProviderInterface::class));

        $attribute = new FromBody();
        $data = new ArrayData();

        $this->expectException(UnexpectedAttributeException::class);
        $this->expectExceptionMessage('Expected "' . FromQuery::class . '", but "' . FromBody::class . '" given.');
        $resolver->prepareData($attribute, $data);
    }

    private function createHydrator(array $queryParams): Hydrator
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($queryParams);

        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        return TestHelper::createHydrator([
            FromQueryResolver::class => new FromQueryResolver($requestProvider),
        ]);
    }
}
