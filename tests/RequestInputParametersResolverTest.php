<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\InputValidationException;
use Yiisoft\Input\Http\Tests\Support\Input\PersonInput;
use Yiisoft\Input\Http\Tests\Support\Input\SimpleRequestInput;
use Yiisoft\Input\Http\Tests\Support\TestHelper;

final class RequestInputParametersResolverTest extends TestCase
{
    public function testBase(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([]);

        $resolver = TestHelper::createRequestInputParametersResolver($request);
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['input'], array_keys($result));

        /** @var PersonInput $input */
        $input = $result['input'];

        $this->assertInstanceOf(PersonInput::class, $input);
        $this->assertFalse($input->getValidationResult()->isValid());
        $this->assertSame(
            ['name' => ['Value cannot be blank.']],
            $input->getValidationResult()->getErrorMessagesIndexedByPath()
        );
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
        $this->assertNull($input->getValidationResult());
        $this->assertSame('Bo', $input->name);
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

    public function testDoNotThrowExceptionForValidatedInputWithoutValidation(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['name' => 'Bo']);

        $resolver = TestHelper::createRequestInputParametersResolver(
            $request,
            useValidatingHydrator: false,
            throwInputValidationException: true,
        );
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame('Bo', $result['input']->name);
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
}
