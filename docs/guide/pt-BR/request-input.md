# Request input (Solicitar entrada)

A entrada da solicitação é um DTO que implementa `RequestInputInterface`:

```php
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;

final class UpdatePostInput implements RequestInputInterface 
{
    public int $id;
    public string $title;
    public string $description = '';
    public string $content;
}
```

Pode ser preparado para ser preenchido automaticamente a partir dos parâmetros da solicitação utilizando atributos:

```php
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;

#[FromBody]
final class UpdatePostInput implements RequestInputInterface 
{
    public int $id;
    public string $title;
    public string $description = '';
    public string $content;
}
```

Para obter mais opções disponíveis, consulte a seção [Atributos do hydrator](hydrator-attributes.md).

O principal conceito de entrada de solicitação é facilitar o preenchimento automático de acordo com o
[resolver](parameters-resolvers.md#requestinputparametersresolver).
No entanto, também é possível usar o hydrator diretamente.

## Combinando com validação

Se você precisar de recursos de validação adicionais, estenda o DTO da classe `AbstractInput` que também implementa
`ValidatedInputInterface`. Desta forma você pode usar atributos de validação para propriedades:

```php
use Yiisoft\Input\Http\Attribute\Data\FromBody

;use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

#[FromBody]
final class UpdatePostInput extends AbstractInput
{
    public function __construct(
        #[Required]
        #[Integer(min: 1)]
        public int $id;
        #[Required]
        #[StringValue]
        #[Length(max: 100)]
        public string $title;
        #[Length(max: 255)]
        #[StringValue]
        public string $description = '';
        #[Required]
        #[StringValue]
        public string $content;
    ) {
    }
}
```

Assim, ao ser preenchido, o DTO poderá ser validado imediatamente, e você poderá recuperar o resultado da validação:

```php
use Yiisoft\Hydrator\Validator\ValidatedInputInterface;

/** @var ValidatedInputInterface $person */
$result = $person->getValidationResult();
$isValid = $result->isValid();
$errorMessagesMap = $result->getErrorMessagesIndexedByPath();
```

Um guia mais completo para trabalhar com o resultado da validação está disponível na
[documentação do validator](https://github.com/yiisoft/validator/blob/1.x/docs/guide/pt-BR/result.md).
