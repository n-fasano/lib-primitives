<?php

declare(strict_types=1);

namespace Fasano\PrimitivesLib;

use Fasano\PrimitivesLib\Metadata\Property\Default\EmptyDescription;
use Fasano\PrimitivesLib\Metadata\Property\Default\EmptyExample;
use Fasano\PrimitivesLib\Metadata\Property\Default\EmptyName;
use Fasano\PrimitivesLib\Metadata\Attribute\Description;
use Fasano\PrimitivesLib\Metadata\Attribute\Example;
use Fasano\PrimitivesLib\Metadata\Attribute\Name;
use Fasano\PrimitivesLib\Metadata\Property\Type;
use Fasano\PrimitivesLib\Metadata\Property\Fqcn;
use Fasano\PrimitivesLib\Metadata\PrimitiveMetadata;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Utilities for working with domain primitives that follow this implicit interface:
 * A class, with a single public property named 'value' that's of a builtin type and that's passed as first constructor argument.
 */
class Primitives
{
    protected static array $reflections = [];
    protected static array $cache = [];

    /**
     * Check if a class represents a domain primitive
     */
    public static function isPrimitive(string $fqcn): bool
    {
        if (!class_exists($fqcn)) {
            return false;
        }

        if (!isset(self::$cache[$fqcn])) {
            self::$cache[$fqcn] = self::_is($fqcn);
        }
        
        return self::$cache[$fqcn];
    }

    public static function getMetadata(string $fqcn): PrimitiveMetadata
    {
        if (!self::isPrimitive($fqcn)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a domain primitive', $fqcn)
            );
        }

        $valueProp = self::checkValueProp($fqcn);
        $valuePropType = self::checkValuePropType($fqcn, $valueProp);

        return new PrimitiveMetadata(
            new Fqcn($fqcn),
            Type::from($valuePropType->getName()),
            self::getAttribute($fqcn, Name::class) ?? new EmptyName,
            self::getAttribute($fqcn, Example::class) ?? new EmptyExample,
            self::getAttribute($fqcn, Description::class) ?? new EmptyDescription,
        );
    }

    /**
     * Extract the value from a domain primitive
     */
    public static function valueOf(object $primitive): mixed
    {
        if (!self::isPrimitive($primitive::class)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a domain primitive', $primitive::class)
            );
        }
        
        return $primitive->value;
    }

    /**
     * Create an instance from a class name and value
     */
    public static function create(string $fqcn, mixed $value): object
    {
        if (!self::isPrimitive($fqcn)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a domain primitive', $fqcn)
            );
        }

        return new $fqcn($value);
    }

    /**
     * Compare two primitives for equality (by value)
     */
    public static function equals(object $a, object $b): bool
    {
        return self::valueOf($a) === self::valueOf($b);
    }

    private static function _is(string $fqcn): bool
    {
        try {
            $valueProp = self::checkValueProp($fqcn);
            self::checkValuePropType($fqcn, $valueProp);

            self::checkConstructorParameter($fqcn);
        } catch (ReflectionException) {
            return false;
        }

        return true;
    }

    /**
     * Must have exactly one public property named 'value'.
     */
    private static function checkValueProp(string $fqcn): ReflectionProperty
    {
        $reflection = self::_reflection($fqcn);
        $publicProps = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        if (count($publicProps) !== 1) {
            throw new ReflectionException(
                sprintf('Expected %s to have a single public property', $fqcn),
            );
        }

        $valueProp = $publicProps[0];
        assert($valueProp instanceof ReflectionProperty);

        $valuePropName = $valueProp->getName();

        if ($valuePropName !== 'value') {
            throw new ReflectionException(
                sprintf('Expected %s\'s sole public property to be named "value"', $fqcn),
            );
        }

        return $valueProp;
    }

    /**
     * Must be a builtin type.
     */
    private static function checkValuePropType(string $fqcn, ReflectionProperty $valueProp): ReflectionNamedType
    {
        $name = $valueProp->getName();
        $type = $valueProp->getType();

        if ($type === null) {
            throw new ReflectionException(
                sprintf('%s::%s should be typed', $fqcn, $name),
            );
        }

        if (!$type instanceof ReflectionNamedType || !$type->isBuiltin()) {
            throw new ReflectionException(
                sprintf('%s::%s should be a builtin type', $fqcn, $name),
            );
        }

        return $type;
    }

    /**
     * Checks that the constructor accepts the value parameter.
     */
    private static function checkConstructorParameter(string $fqcn): ReflectionParameter
    {
        $reflection = self::_reflection($fqcn);

        if (!$constructor = $reflection->getConstructor()) {
            throw new ReflectionException(
                sprintf('%s does not have a constructor', $fqcn),
            );
        }

        if (empty($parameters = $constructor->getParameters())) {
            throw new ReflectionException(
                sprintf('%s::__construct() does not accept parameters', $fqcn),
            );
        }

        $valueParameter = $parameters[0];
        assert($valueParameter instanceof ReflectionParameter);

        if ($valueParameter->getName() !== 'value') {
            throw new ReflectionException(
                sprintf('Expected %s::__construct()\'s first parameter to be named "value"', $fqcn),
            );
        }

        return $valueParameter;
    }

    private static function getAttribute(string $fqcn, string $attrFqcn): mixed
    {
        $reflectionAttr = self::_reflection($fqcn)->getAttributes($attrFqcn)[0] ?? null;

        return $reflectionAttr->newInstance();
    }

    private static function _reflection(string $fqcn): ReflectionClass
    {
        return self::$reflections[$fqcn] ??= new ReflectionClass($fqcn);
    }
}