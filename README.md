<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Input HTTP</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/input-http/v/stable.png)](https://packagist.org/packages/yiisoft/input-http)
[![Total Downloads](https://poser.pugx.org/yiisoft/input-http/downloads.png)](https://packagist.org/packages/yiisoft/input-http)
[![Build status](https://github.com/yiisoft/input-http/workflows/build/badge.svg)](https://github.com/yiisoft/input-http/actions?query=workflow%3Abuild)
[![Code Coverage](https://codecov.io/gh/yiisoft/input-http/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/input-http)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Finput-http%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/input-http/master)
[![static analysis](https://github.com/yiisoft/input-http/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/input-http/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/input-http/coverage.svg)](https://shepherd.dev/github/yiisoft/input-http)
[![psalm-level](https://shepherd.dev/github/yiisoft/input-http/level.svg)](https://shepherd.dev/github/yiisoft/input-http)

The package provides [Yii Hydrator](https://github.com/yiisoft/hydrator) attributes
to get data from [PSR-7 HTTP request](https://www.php-fig.org/psr/psr-7/) and adds extra abilities to middlewares
processed by [Yii Middleware Dispatcher](https://github.com/yiisoft/middleware-dispatcher):
- maps data from PSR-7 HTTP request to PHP DTO representing user input;
- uses Yii Hydrator parameter attributes for resolving middleware parameters.

## Requirements

- PHP 8.0 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/input-http
```

## General usage

First of all, you need to store a request object into request provider. There are three ways to do it:

1) Add `\Yiisoft\Input\Http\Request\Catcher\RequestCatcherMiddleware` to your application middleware stack.

2) Add `\Yiisoft\Input\Http\Request\Catcher\RequestCatcherParametersResolver` to your middleware parameters resolver
in `Yiisoft\Middleware\Dispatcher\MiddlewareFactory`. It is usually used as additional parameters resolver in composite
parameters resolver. Example parameters resolver configuration for Yii DI container:

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

3) Manually:

    ```php
    /** 
     * @var \Yiisoft\Input\Http\Request\RequestProviderInterface $requestProvider
     * @var \Psr\Http\Message\ServerRequestInterface $request
     */
    $requestProvider->set($request);
    ```

To use the package, you need to create a DTO class and mark its properties with attributes:

```php
use \Yiisoft\Input\Http\Attribute\Parameter\Query;
use \Yiisoft\Input\Http\Attribute\Parameter\Body;
use \Yiisoft\Input\Http\Attribute\Parameter\UploadedFiles;

final class EditPostInput
{
    public function __construct(
        #[Query]
        private string $id,
        #[Body]        
        private string $title,
        #[Body]        
        private string $content,
        #[UploadedFiles('uploads')]        
        private $uploads,
    )
    {
    
    }

    // getters
        
} 
```

Post id will be mapped from query parameter `id`, title and content will be mapped from request body and uploads will
be mapped from request uploaded files.

Additionally, you can fill a property from request attribute using `#[Request('attributeName')]`.
This is useful when middleware prior writes the value.

Instead of mapping each property, you can use the following:

```php
use \Yiisoft\Input\Http\Attribute\Data\FromQuery;
use \Yiisoft\Input\Http\Attribute\Data\FromBody; 

#[FromQuery]
final class SearchInput
{
    public function __construct(
        private string $query,
        private string $category,
    ) {}
    
    // getters
}

#[FromBody]
final class CreateUserInput
{
    public function __construct(
        private string $username,
        private string $email,
    ) {}
    
    // getters
}
```

`SearchInput` will be mapped from query parameters, `CreateUserInput` will be mapped from parsed request body.
Both will expect request parameters in request named same as DTO properties.

## Documentation

- [Guide](docs/guide/en/README.md)
- [Internals](docs/internals.md)

## License

The Yii Input HTTP is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
