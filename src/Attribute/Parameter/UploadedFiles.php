<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Parameter;

use Attribute;
use Yiisoft\Hydrator\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class UploadedFiles implements ParameterAttributeInterface
{
    public function __construct(
        private string|array|null $name = null
    )
    {
    }

    public function getName(): string|array|null
    {
        return $this->name;
    }

    public function getResolver(): string
    {
        return UploadedFilesResolver::class;
    }
}
