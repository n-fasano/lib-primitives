<?php

namespace Fasano\PrimitivesLib\Metadata\Property;

use Fasano\PrimitivesLib\Metadata\Attribute;

#[Attribute\Name('Type')]
#[Attribute\Example('string')]
#[Attribute\Description("The primitive's builtin PHP type")]
enum Type: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case ARRAY = 'array';

    public static function fromVar(mixed $var): self
    {
        return self::from(gettype($var));
    }
}