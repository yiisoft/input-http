<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Data;

use Attribute;
use Yiisoft\Hydrator\DataAttributeInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final class FromBody implements DataAttributeInterface
{
    public function __construct(
        private string|array|null $name = null,
        private array $map = [],
        private bool $strict = false,
    )
    {
    }

    public function getName(): string|array|null
    {
        return $this->name;
    }

    public function getMap(): array
    {
        return $this->map;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function getResolver(): string
    {
        return FromBodyResolver::class;
    }
}
