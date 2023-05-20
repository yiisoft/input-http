<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Parameter;

use Attribute;
use Yiisoft\Hydrator\ParameterAttributeInterface;

/**
 * Take data for the property or attribute from the query string.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class Query implements ParameterAttributeInterface
{
    /**
     * @param array|string|null $name The field in the query string to get data from.
     * @psalm-param string|list<string>|null $name
     */
    public function __construct(
        private string|array|null $name = null
    ) {
    }

    /**
     * Get the name of the field in the query string to get data from.
     *
     * @return array|string|null The field in the query string to get data from.
     * @psalm-return string|list<string>|null
     */
    public function getName(): string|array|null
    {
        return $this->name;
    }

    public function getResolver(): string
    {
        return QueryResolver::class;
    }
}
