<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\Validator\Attribute\ValidateResolver;
use Yiisoft\Hydrator\Validator\ValidatingHydrator;
use Yiisoft\Input\Http\Attribute\Data\FromQueryResolver;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\Input\Http\Tests\Support\Input\PersonInput;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Validator\Validator;

final class RequestInputParametersResolverTest extends TestCase
{
    public function testBase(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([]);

        $resolver = $this->createResolver($request);
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

        $resolver = $this->createResolver($request, useValidatingHydrator: false);
        $parameters = TestHelper::getParameters(static fn(PersonInput $input) => null);

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['input'], array_keys($result));

        /** @var PersonInput $input */
        $input = $result['input'];

        $this->assertInstanceOf(PersonInput::class, $input);
        $this->assertNull($input->getValidationResult());
        $this->assertSame('Bo', $input->name);
    }

    private function createResolver(
        ServerRequestInterface $request,
        bool $useValidatingHydrator = true,
    ): RequestInputParametersResolver {
        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $validator = new Validator();
        $validateResolver = new ValidateResolver($validator);
        $container = new SimpleContainer(
            [
                ValidateResolver::class => $validateResolver,
                FromQueryResolver::class => new FromQueryResolver($requestProvider),
            ],
        );

        if ($useValidatingHydrator) {
            $hydrator = new ValidatingHydrator(
                new Hydrator($container),
                $validator,
                $validateResolver,
            );
        } else {
            $hydrator = new Hydrator($container);
        }

        return new RequestInputParametersResolver($hydrator);
    }
}
