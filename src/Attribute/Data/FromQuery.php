<?php

declare(strict_types=1);

namespace Yiisoft\Input\Http\Attribute\Data;

use Attribute;
use Yiisoft\Hydrator\DataAttributeInterface;
use Yiisoft\Hydrator\HydratorInterface;

/**
 * @psalm-import-type MapType from HydratorInterface
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class FromQuery implements DataAttributeInterface
{
    /**
     * @psalm-param string|list<string>|null $name
     * @psalm-param MapType $map
     */
    public function __construct(
        private string|array|null $name = null,
        private array $map = [],
        private bool $strict = false,
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

    /**
     * @psalm-return MapType
     */
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
        return FromQueryResolver::class;
    }
}
