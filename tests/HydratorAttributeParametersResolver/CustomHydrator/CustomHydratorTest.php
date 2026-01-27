<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests\HydratorAttributeParametersResolver\CustomHydrator;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\TypeCaster\TypeCastContext;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\BodyResolver;
use Yiisoft\Input\Http\HydratorAttributeParametersResolver;
use Yiisoft\Input\Http\Tests\Support\CallableTypeCaster;
use Yiisoft\Input\Http\Tests\Support\TestHelper;
use Yiisoft\RequestProvider\RequestProvider;

final class CustomHydratorTest extends TestCase
{
    public function testBase(): void
    {
        $request = new ServerRequest(parsedBody: ['user' => 'Vasya']);
        $requestProvider = new RequestProvider();
        $requestProvider->set($request);

        $resolver = new HydratorAttributeParametersResolver(
            TestHelper::createParameterAttributesHandler([
                BodyResolver::class => new BodyResolver($requestProvider),
            ]),
            typeCaster: new CallableTypeCaster(
                static fn(mixed $value, TypeCastContext $context) => $context->getHydrator()->create(User::class, [
                    'name' => $value,
                ]),
            ),
            hydrator: new Hydrator(
                new CallableTypeCaster(
                    static fn(mixed $value) => 'The ' . $value,
                ),
            ),
        );

        $parameters = TestHelper::getParameters(
            static fn(
                #[Body]
                User $user,
            ) => null,
        );

        $result = $resolver->resolve($parameters, $request);

        $this->assertSame(['user'], array_keys($result));
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertSame('The Vasya', $result['user']->name);
    }
}
