<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Di\StateResetter;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\RequestProvider\RequestNotSetException;
use Yiisoft\RequestProvider\RequestProvider;
use Yiisoft\RequestProvider\RequestProviderInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

use function dirname;

final class ConfigTest extends TestCase
{
    public function testDi(): void
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(RequestProvider::class, $container->get(RequestProviderInterface::class));
        $this->assertInstanceOf(
            RequestInputParametersResolver::class,
            $container->get(RequestInputParametersResolver::class)
        );
    }

    public function testResetRequestProvider(): void
    {
        $container = $this->createContainer();

        $requestProvider = $container->get(RequestProviderInterface::class);
        $requestProvider->set($this->createMock(ServerRequestInterface::class));

        $container->get(StateResetter::class)->reset();

        $this->expectException(RequestNotSetException::class);
        $requestProvider->get();
    }

    private function createContainer(): Container
    {
        return new Container(
            ContainerConfig::create()->withDefinitions($this->getContainerDefinitions())
        );
    }

    private function getContainerDefinitions(array|null $params = null): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }

        return array_merge(
            require dirname(__DIR__) . '/vendor/yiisoft/request-provider/config/di-web.php',
            require dirname(__DIR__) . '/config/di-web.php',
            [
                HydratorInterface::class => Hydrator::class,
                ValidatorInterface::class => Validator::class,
            ],
        );
    }

    private function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params-web.php';
    }
}
