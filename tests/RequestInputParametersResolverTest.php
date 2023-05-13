<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Input\Http\Tests\Support\Input\PersonInput;
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
}
