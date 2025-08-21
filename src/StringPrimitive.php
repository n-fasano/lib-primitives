<?php

namespace Fasano\PrimitivesLib;

abstract readonly class StringPrimitive
{
    public function __construct(public string $value)
    {
        if (false === static::check($value)) {
            throw new \InvalidArgumentException(
                sprintf('%s is not a valid %s', $value, static::class),
            );
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    abstract public static function check(string $value): bool;
}