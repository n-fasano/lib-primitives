<?php

namespace Fasano\PrimitivesLib\Metadata\Property;

use Fasano\PrimitivesLib\Metadata\Attribute;
use Fasano\PrimitivesLib\StringPrimitive;

#[Attribute\Name('Fully qualified class name')]
#[Attribute\Example('Acme\\Property\\Email')]
#[Attribute\Description("The primitive's FQCN")]
readonly class Fqcn extends StringPrimitive
{
    private const string PATTERN = '/^\\?[A-Za-z_][A-Za-z0-9_]*(?:\\[A-Za-z_][A-Za-z0-9_]*)*$/';

    public static function check(string $value): bool
    {
        return false !== preg_match(self::PATTERN, $value);
    }
}