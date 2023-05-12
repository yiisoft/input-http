<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Parameter;

use Attribute;
use Yiisoft\Hydrator\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class UploadedFiles implements ParameterAttributeInterface
{
    /**
     * @psalm-param string|list<string>|null $name
     */
    public function __construct(
        private string|array|null $name = null
    )
    {
    }

    /**
     * @psalm-return string|list<string>|null
     */
    public function getName(): string|array|null
    {
        return $this->name;
    }

    public function getResolver(): string
    {
        return UploadedFilesResolver::class;
    }
}
