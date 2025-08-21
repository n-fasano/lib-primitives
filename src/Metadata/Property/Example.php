<?php

namespace Fasano\PrimitivesLib\Metadata\Property;

use Fasano\PrimitivesLib\Metadata\Attribute;
use Fasano\PrimitivesLib\StringPrimitive;

#[Attribute\Name('Example')]
#[Attribute\Example('Any valid value(s)')]
#[Attribute\Description('Example(s) of valid value(s) for the primitive')]
readonly class Example extends StringPrimitive
{
    public static function check(string $value): bool
    {
        return true;
    }
}