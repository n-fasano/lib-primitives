<?php

namespace Fasano\PrimitivesLib\Metadata\Property;

use Fasano\PrimitivesLib\Metadata\Attribute;
use Fasano\PrimitivesLib\StringPrimitive;

#[Attribute\Name('Description')]
#[Attribute\Example('This primitive defines a...')]
#[Attribute\Description("The primitive's description")]
readonly class Description extends StringPrimitive
{
    public static function check(string $value): bool
    {
        return true;
    }
}