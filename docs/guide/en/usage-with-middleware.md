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
        
        /** @var ResponseInterface */
        return $response;
    }
}
```

An example of applying parameters' resolver to middleware dispatcher:

```php
use Psr\Container\ContainerInterface;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

/**
 * @var ContainerInterface $container
 * @var ParametersResolverInterface $resolver
 * @var $eventDispatcher 
 */
$dispatcher = new MiddlewareDispatcher(new MiddlewareFactory($container, $resolver), $eventDispatcher);
```

Using within a group of resolvers:

```php
use Psr\Container\ContainerInterface;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

/** 
 * @var ContainerInterface $container
 * @var ParametersResolverInterface $resolver1
 * @var ParametersResolverInterface $resolver2 
 */
$dispatcher = new MiddlewareDispatcher(
    new MiddlewareFactory($container, new CompositeParametersResolver($resolver1, $resolver2)),
    $eventDispatcher,
);
```
