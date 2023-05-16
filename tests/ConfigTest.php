<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Config\Config;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Input\Http\Request\RequestProvider;
use Yiisoft\Input\Http\Request\RequestProviderInterface;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use function dirname;

final class ConfigTest extends TestCase
{
    public function testDi(): void
    {
        $container = new Container(
            ContainerConfig::create()->withDefinitions($this->getContainerDefinitions())
        );

        $this->assertInstanceOf(RequestProvider::class, $container->get(RequestProviderInterface::class));
        $this->assertInstanceOf(
            RequestInputParametersResolver::class,
            $container->get(RequestInputParametersResolver::class)
        );
    }

    private function getContainerDefinitions(array|null $params = null): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }

        return array_merge(
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
