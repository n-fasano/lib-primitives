<?php

namespace Fasano\PrimitivesLib\Metadata\Property;

use Fasano\PrimitivesLib\Metadata\Attribute;
use Fasano\PrimitivesLib\StringPrimitive;

#[Attribute\Name('Name')]
#[Attribute\Example('Primitive metadata')]
#[Attribute\Description("A human-readable name for the primitive")]
readonly class Name extends StringPrimitive
{
    public static function check(string $value): bool
    {
        return true;
    }
}