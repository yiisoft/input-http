# Storing request

In order for this package to be able to parse request's data, you need to store a request object into request provider. 
There are 3 ways to do it:

1) Add `Yiisoft\Input\Http\Request\Catcher\RequestCatcherMiddleware` to your application middleware stack.

2) Add `Yiisoft\Input\Http\Request\Catcher\RequestCatcherParametersResolver` to your middleware parameters resolver
   in `Yiisoft\Middleware\Dispatcher\MiddlewareFactory`. It is usually used as additional parameters resolver in 
   composite parameters resolver. Example parameters resolver configuration for Yii DI container:

    ```php
    use Yiisoft\Definitions\Reference;
    use Yiisoft\Input\Http\Request\Catcher\RequestCatcherParametersResolver;
    use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
    use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;
    
    ParametersResolverInterface::class => [
        'class' => CompositeParametersResolver::class,
        '__construct()' => [
            Reference::to(RequestCatcherParametersResolver::class),
            // ...
        ],
    ],
    ```

3) Add request object manually:

    ```php
    use Psr\Http\Message\ServerRequestInterface;
    use Yiisoft\Input\Http\Request\RequestProviderInterface;
    
    /** 
     * @var RequestProviderInterface $requestProvider
     * @var ServerRequestInterface $request
     */
    $requestProvider->set($request);
    ```
