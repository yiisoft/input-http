# Atributos do Hydrator

Este pacote fornece alguns atributos adicionais para [hydrator](https://github.com/yiisoft/hydrator).

## Atributos de parâmetro

Esses atributos são usados para um único parâmetro DTO:

- `#[Query]` - mapeia com o parâmetro de consulta da solicitação.
- `#[Body]` - mapeia com o parâmetro body da solicitação.
- `#[Request]` - mapeia com o atributo da solicitação. Isso é útil quando o middleware grava o valor antes.
- `#[UploadedFiles]` - mapeia com os arquivos carregados da solicitação.

O uso de todos os parâmetros disponíveis é mostrado abaixo no exemplo:

```php
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Input\Http\Attribute\Parameter\Request;use \Yiisoft\Input\Http\Attribute\Parameter\UploadedFiles;

final class UpdatePostInput
{
    public function __construct(
        #[Query]
        private string $id,
        #[Body]        
        private string $title,
        #[Body]        
        private string $content,
        #[UploadedFiles]        
        private $uploads,
        #[Request]
        private string $clientInfo = '';
    ) {
    }

    // getters       
} 
```

Aqui:

- O ID do post será mapeado a partir do parâmetro de consulta da solicitação;
- Título e conteúdo serão mapeados a partir do corpo da solicitação;
- Os uploads serão mapeados a partir dos arquivos carregados da solicitação;
- As informações do cliente serão mapeadas a partir do atributo da solicitação.

### Personalização

Por padrão, espera-se que os parâmetros de solicitação tenham o mesmo nome das propriedades DTO. Para mudar isso, passe o nome
ao anexar o atributo:

```php
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\Attribute\Parameter\Query;
use Yiisoft\Input\Http\Attribute\Parameter\Request;
use Yiisoft\Input\Http\Attribute\Parameter\UploadedFiles;

final class UpdatePostInput
{
    public function __construct(
        #[Query('post_id')]
        private string $id,
        #[Body('post_title')]        
        private string $title,
        #[Body('post_content')]        
        private string $content,
        #[UploadedFiles('post_uploads')]        
        private $uploads,
        #[Request('clientData')]
        private string $clientInfo = '';
    ) {
    }   
} 
```

## Atributos de dados

Eles são mais organizados quando a fonte dos valores é comum:

- `#[FromQuery]` - mapeia com parâmetros de consulta da solicitação.
- `#[FromBody]` - mapeia com os parâmetros do corpo da solicitação.

A diferença aqui é que esses atributos estão vinculados à classe como um todo, e não a um atributo individual:

```php
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Input\Http\Attribute\Data\FromQuery; 

#[FromQuery]
final class SearchInput
{
    public function __construct(
        private string $query,
        private string $category,
    ) {
    }
    
    // getters
}

#[FromBody]
final class CreateUserInput
{
    public function __construct(
        private string $username,
        private string $email,
    ) {
    }
    
    // getters
}
```

`SearchInput` será mapeado a partir dos parâmetros de consulta, enquanto `CreateUserInput` será mapeado a partir do corpo da solicitação analisado.

### Personalização dos nomes dos parâmetros

Semelhante aos atributos de parâmetro, os nomes dos parâmetros da solicitação podem ser customizados. Aqui isso é feito através de um mapa onde as
chaves são nomes e valores de propriedades DTO de acordo com os nomes dos parâmetros da solicitação, que são esperados. Além disso, você
pode restringir o escopo de onde exatamente analisar os parâmetros da solicitação. Além disso, é possível lançar uma exceção
quando existem alguns parâmetros presentes no escopo da solicitação selecionada que não foram especificados no mapa.

```php
use Yiisoft\Input\Http\Attribute\Data\FromBody;
use Yiisoft\Input\Http\Attribute\Data\FromQuery; 

#[FromQuery(
    'search', // Use an array for bigger level of nesting, for example, `['client', 'search']`. 
    ['query' => 'q', 'category' => 'c'], 
    strict: true,
)]
final class SearchInput
{
    public function __construct(
        private string $query,
        private string $category,
    ) {
    }
}
```

No exemplo acima:

- A string de consulta esperada no formato como - `?search[q]=input&search[c]=package`. O valor `input` é mapeado para a
propriedade `$query`, enquanto o valor `package` - para a propriedade `$category`.
- Se a string de consulta contiver parâmetros extras dentro do escopo selecionado, a exceção será lançada -
`?search[q]=input&search[c]=pacote&search[s]=desc`. Parâmetros extras fora do escopo são permitidos -
`?search[q]=input&search[c]=pacote&user=john`.
