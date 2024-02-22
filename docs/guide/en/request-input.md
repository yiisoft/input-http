# Request input

Request input is a DTO implementing `RequestInputInterface`:

```php
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;

final class UpdatePostInput implements RequestInputInterface 
{
    public int $id;
    public string $name;
    public string $description = '';
    public string $content;
}
```

It can be filled automatically from request's query / body parameters using attributes:

```php
use Yiisoft\Input\Http\AbstractInput;
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;

#[FromBody] // Use #[FromQuery] for filling from query parameters.
final class UpdatePostInput implements RequestInputInterface 
{
    public int $id;
    public string $name;
    public string $description = '';
    public string $content;
}
```

To enable automatic filling, include according 
[resolver](usage-with-middleware-dispatcher.md#requestinputparametersresolver) to middleware dispatcher.

If you need validation features additionally, just extend the DTO from `AbstractInput` class which also implements 
`ValidatedInputInterface`. This way you can use validation attributes for properties:

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
        public string $name;
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

Thus, when filled, the DTO can be validated right away, and you will be able to retrieve the validation result:

```php
use Yiisoft\Hydrator\Validator\ValidatedInputInterface;

/** @var ValidatedInputInterface $person */
$result = $person->getValidationResult();
$isValid = $result->isValid();
$errorMessagesMap = $result->getErrorMessagesIndexedByPath();
```

More thorough guide for working with the validation result is available in the 
[validator guide](https://github.com/yiisoft/validator/blob/1.x/docs/guide/en/result.md). 
