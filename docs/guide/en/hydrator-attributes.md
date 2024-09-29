# Hydrator attributes

This package provides some additional attributes for [hydrator](https://github.com/yiisoft/hydrator).

## Parameter attributes

These attributes are used for a single DTO parameter:

- `#[Query]` - maps with request's query parameter.
- `#[Body]` - maps with request's body parameter.
- `#[Request]` - maps with request's attribute. This is useful when middleware prior writes the value.
- `#[UploadedFiles]` - maps with request's uploaded files.

The usage of all available parameters is shown below in the single example:

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

Here:

- Post id will be mapped from request's query parameter;
- Title and content will be mapped from request's body;
- Uploads will be mapped from request's uploaded files;
- Client info will be mapped from request's attribute.

> Note: `Body` attribute assumes that request body is parsed. If your implementation of request doesn't parse body
> automatically, you can use [yiisoft/request-body-parser](https://github.com/yiisoft/request-body-parser) middleware
> to prepare it.

### Customization

By default, request parameters are expected to have the same name as DTO properties. To change that, pass the name
when attaching the attribute:

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

## Data attributes

They are neater when the source of values is common:

- `#[FromQuery]` - maps with request's query parameters.
- `#[FromBody]` - maps with request's body parameters.

The difference here is that these attributes are attached to the class as a whole, not to an individual attribute:

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

`SearchInput` will be mapped from query parameters, while `CreateUserInput` will be mapped from parsed request body.

> Note: `FromBody` attribute assumes that request body is parsed. If your implementation of request doesn't parse body
> automatically, you can use [yiisoft/request-body-parser](https://github.com/yiisoft/request-body-parser) middleware
> to prepare it.

### Customizing parameter names

Similar to parameter attributes, the names of request's parameters can be customized. Here it's done via a map where
keys are DTO properties' names and values according to request's parameter names, which are expected. Besides that, you
can narrow down the scope where exactly to parse the request parameters from. Also, it's possible to throw an exception
when there are some parameters present in the selected request's scope that were not specified in the map.

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

In the above example:

- The query string expected in the format such as - `?search[q]=input&search[c]=package`. `input` value is mapped to
`$query` property, while `package` value - to `$category` property.
- If the query string contains extra parameters within the selected scope, the exception will be thrown -
`?search[q]=input&search[c]=package&search[s]=desc`. Extra parameters outside the scope are allowed though -
`?search[q]=input&search[c]=package&user=john`.
