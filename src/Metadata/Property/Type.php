<?php

namespace Fasano\PrimitivesLib\Metadata\Property;

use Fasano\PrimitivesLib\Metadata\Attribute;

#[Attribute\Name('Type')]
#[Attribute\Example('string')]
#[Attribute\Description("A PHP scalar type")]
enum Type: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';

    public static function fromVar(mixed $var): self
    {
        return self::from(gettype($var));
    }
}