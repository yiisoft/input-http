<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use Closure;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\InputValidationException;
use Yiisoft\Input\Http\Tests\Support\Input\PersonInput;
use Yiisoft\Input\Http\Tests\Support\Input\SimpleRequestInput;
use Yiisoft\Input\Http\Tests\Support\PureObject;
use Yiisoft\Input\Http\Tests\Support\TestHelper;

final class RequestInputParametersResolverTest extends TestCase
{
    public function testBase(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['a' => '1', 'b' => '2']);

        $resolver = TestHelper::createRequestInputParametersResolver($request);
        $parameters = TestHelper::getParameters(static fn(PersonInput $person, SimpleRequestInput $simple) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['person', 'simple'], array_keys($result));

        /** @var PersonInput $person */
        $person = $result['person'];

        /** @var SimpleRequestInput $simple */
        $simple = $result['simple'];

        $this->assertInstanceOf(PersonInput::class, $person);
        $this->assertFalse($person->getValidationResult()->isValid());
        $this->assertSame(
            ['name' => ['Name is required.']],
            $person->getValidationResult()->getErrorMessagesIndexedByPath()
        );

        $this->assertInstanceOf(SimpleRequestInput::class, $simple);
        $this->assertSame('1', $simple->a);
        $this->assertSame('2', $simple->b);
    }

    public static function dataParameters(): array
    {
        return [
            [
                ['person' => PersonInput::class, 'simple' => SimpleRequestInput::class],
                static fn(PersonInput $person, SimpleRequestInput $simple) => null,
            ],
            [
                ['person' => PersonInput::class],
                static fn(PersonInput $person, PureObject $object) => null,
            ],
            [
                ['simple' => SimpleRequestInput::class],
                static fn(PureObject|SimpleRequestInput $object, SimpleRequestInput $simple) => null,
            ],
        ];
    }

    #[DataProvider('dataParameters')]
    public function testParameters(array $expected, Closure $closure): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['a' => '1', 'b' => '2']);

        $resolver = TestHelper::createRequestInputParametersResolver($request);
        $parameters = TestHelper::getParameters($closure);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(array_keys($expected), array_keys($result));

        foreach ($result as $name => $value) {
            $this->assertInstanceOf($expected[$name], $value);
        }
    }

    public function testWithNonValidatingHydrator(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['name' => 'Bo']);

        $resolver = TestHelper::createRequestInputParametersResolver($request, useValidatingHydrator: false);
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['input'], array_keys($result));

        /** @var PersonInput $input */
        $input = $result['input'];

        $this->assertInstanceOf(PersonInput::class, $input);
        $this->assertSame('Bo', $input->name);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Validation result is not set.');
        $input->getValidationResult();
    }

    public function testDoNotThrowExceptionForNonValidatedInput(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['a' => '1', 'b' => '2']);

        $resolver = TestHelper::createRequestInputParametersResolver(
            $request,
            throwInputValidationException: true,
        );
        $parameters = TestHelper::getParameters(static fn(SimpleRequestInput $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['input'], array_keys($result));

        /** @var SimpleRequestInput $input */
        $input = $result['input'];

        $this->assertInstanceOf(SimpleRequestInput::class, $input);
        $this->assertSame('1', $input->a);
        $this->assertSame('2', $input->b);
    }

    public function testDoNotThrowExceptionForValidInput(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['name' => 'Bo']);

        $resolver = TestHelper::createRequestInputParametersResolver(
            $request,
            throwInputValidationException: true,
        );
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame('Bo', $result['input']->name);
    }

    public function testValidatedInputWithoutValidation(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['name' => 'Bo']);

        $resolver = TestHelper::createRequestInputParametersResolver(
            $request,
            useValidatingHydrator: false,
            throwInputValidationException: true,
        );
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Validation result is not set.');
        $resolver->resolve($parameters, $request);
    }

    public function testThrowExceptionForInvalidInput(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['name' => '']);

        $resolver = TestHelper::createRequestInputParametersResolver(
            $request,
            throwInputValidationException: true,
        );
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $this->expectException(InputValidationException::class);
        $resolver->resolve($parameters, $request);
    }

    public function testUnionType(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([]);

        $resolver = TestHelper::createRequestInputParametersResolver($request);
        $parameters = TestHelper::getParameters(static fn(PersonInput|string $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame([], $result);
    }
}
