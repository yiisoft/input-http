# Parameters' resolvers (Resolvedores de parâmetros)

Este pacote oferece várias
[implementações personalizadas do resolvedor de parâmetros](https://github.com/yiisoft/middleware-dispatcher?tab=readme-ov-file#creating-your-own-implementation-of-parameters-resolver)
do pacote [Yii Middleware Dispatcher](https://github.com/yiisoft/middleware-dispatcher):

- [`HydratorAttributeParametersResolver`](#hydratorattributeparametersresolver)
- [`RequestInputParametersResolver`](#requestinputparametersresolver)

## `HydratorAttributeParametersResolver`

Permite usar atributos do hydrator no middleware executado pelo despachante do middleware.

O caso de uso prático é mapear parâmetros de consulta para argumentos da ação do controlador:

```php
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Input\Http\Attribute\Parameter\Query;

final class PostController
{
    public function get(#[Query('id')] string $id): ResponseInterface
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

É útil se você não precisa do DTO.

Você pode alterar a dica de tipo do argumento para conversão automática de tipo:

```php
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Input\Http\Attribute\Parameter\Query;

final class PostController
{
    public function get(#[Query('id')] int $id): ResponseInterface
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

### Adicionando ao middleware dispatcher

Um exemplo de aplicação do parameters' resolver para o middleware dispatcher:

```php
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Hydrator\AttributeHandling\ParameterAttributesHandler;
use Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory;
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Hydrator\TypeCaster\TypeCasterInterface;
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

Mais informações sobre dependências do pacote [Yii Hydrator](https://github.com/yiisoft/hydrator) podem ser encontradas em sua documentação
e

- [attribute resolver factory](https://github.com/yiisoft/hydrator/blob/master/docs/guide/en/attribute-resolver-factory.md)
- [type casting](https://github.com/yiisoft/hydrator/blob/master/docs/guide/en/typecasting.md)
- [hydrator](https://github.com/yiisoft/hydrator)

Usando dentro de um grupo de resolvers:

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

## `RequestInputParametersResolver`

Permite usar classes [request input](request-input.md) como dicas de tipo no middleware executado pelo middleware dispatcher.

O caso de uso prático é mapear classes de entrada de solicitação para argumentos de ação do controlador:

```php
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;

#[FromBody]
final class UpdatePostInput extends AbstractInput
{
    public int $id;
    public string $title;
    public string $description = '';
    public string $content;
}

final class UpdatePostController
{
    public function update(UpdatePostInput $post): ResponseInterface
    {
        // ...
        
        /** @var ResponseInterface $response */
        return $response;
    }
}
```

O DTO usado é [request input](request-input.md) com [atributo do hydrator](hydrator-attributes.md) aplicado.

> Como alternativa, [Yii Validating Hydrator](https://github.com/yiisoft/hydrator-validator) pode ser usado em vez disso para
> validação automática de solicitação de entrada DTO.

### Adicionando ao middleware dispatcher

O processo é muito semelhante a [adicionar `HydratorAttributeParametersResolver`](#adicionando-ao-middleware-dispatcher); espere a
inicialização do resolver (dependências):

```php
use Yiisoft\Hydrator\HydratorInterface;
use Yiisoft\Input\Http\RequestInputParametersResolver;

/** @var HydratorInterface $hydrator */
$resolver = new RequestInputParametersResolver(
    $hydrator, 
    // This allows throwing the exception when the validation result is not valid.
    throwInputValidationException: true,
);
```
