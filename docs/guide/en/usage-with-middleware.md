# Usage with middleware

`HydratorAttributeParametersResolver` is a 
[custom implementation pf parameters resolver](https://github.com/yiisoft/middleware-dispatcher?tab=readme-ov-file#creating-your-own-implementation-of-parameters-resolver) 
from [Yii Middleware Dispatcher](https://github.com/yiisoft/middleware-dispatcher) package. It allows to use hydrator 
attributes in middleware executed by middleware dispatcher.

The practical use case is mapping query parameters to controller action's arguments:

```php
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Input\Http\Attribute\Parameter\Query;

final class PostsController
{
    public function update(#[Query('id')] string $id): ResponseInterface
    {
        // ...
        
        /**
         * @var string $id 
         * @var ResponseInterface $response 
         */
        return $response;
    }
}
```

You can change argument's type hint for automatic type casting:

```php
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Input\Http\Attribute\Parameter\Query;

final class PostsController
{
    public function update(#[Query('id')] int $id): ResponseInterface
    {
        // ...
        
        /**
         * @var int $id 
         * @var ResponseInterface $response 
         */
        return $response;
    }
}
```

An example of applying parameters' resolver to middleware dispatcher:

```php
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\HydratorInterface;use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;
use Yiisoft\Input\Http\HydratorAttributeParametersResolver;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

/**
 * @var ContainerInterface $container
 * @var EventDispatcherInterface $eventDispatcher
 * @var TypeCasterInterface $typeCaster 
 * @var HydratorInterface $hydrator
 */
$resolver = new HydratorAttributeParametersResolver(
    new ParameterAttributesHandler(new ContainerAttributeResolverFactory($container)),
    $typeCaster, // Optional, for customizing type casting
    $hydrator, // Optional, for customizing hydration
);
$dispatcher = new MiddlewareDispatcher(new MiddlewareFactory($container, $resolver), $eventDispatcher);
```

More info about dependencies from [Yii Hydrator](https://github.com/yiisoft/hydrator) package can be found in its docs 
and guide:
 
- [attribute resolver factory](https://github.com/yiisoft/hydrator/blob/master/docs/guide/en/attribute-resolver-factory.md)
- [type casting](https://github.com/yiisoft/hydrator/blob/master/docs/guide/en/typecasting.md) 
- [hydrator](https://github.com/yiisoft/hydrator)

Using within a group of resolvers:

```php
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Input\Http\HydratorAttributeParametersResolver;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

/** 
 * @var ContainerInterface $container
 * @var ParametersResolverInterface $existingResolver1
 * @var ParametersResolverInterface $existingResolver2
 * @var HydratorAttributeParametersResolver $addedResolver
 * @var EventDispatcherInterface $eventDispatcher 
 */
$dispatcher = new MiddlewareDispatcher(
    new MiddlewareFactory(
        $container, 
        new CompositeParametersResolver($existingResolver1, $existingResolver1, $addedResolver),
    ),
    $eventDispatcher,
);
```
