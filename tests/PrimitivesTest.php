<?php

declare(strict_types=1);

namespace Fasano\PrimitivesLib\Tests;

use Fasano\PrimitivesLib\Metadata\PrimitiveMetadata;
use Fasano\PrimitivesLib\Metadata\Property\Description;
use Fasano\PrimitivesLib\Metadata\Property\Example;
use Fasano\PrimitivesLib\Metadata\Property\Fqcn;
use Fasano\PrimitivesLib\Metadata\Property\Name;
use Fasano\PrimitivesLib\Metadata\Property\Type;
use Fasano\PrimitivesLib\Primitives;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PrimitivesTest extends TestCase
{
    public function testIsPrimitiveWithValidClasses(): void
    {
        $this->assertTrue(Primitives::isPrimitive(Fqcn::class));
        $this->assertTrue(Primitives::isPrimitive(Name::class));
        $this->assertTrue(Primitives::isPrimitive(Example::class));
        $this->assertTrue(Primitives::isPrimitive(Description::class));
    }

    public function testIsPrimitiveWithInvalidClasses(): void
    {
        $this->assertFalse(Primitives::isPrimitive(PrimitiveMetadata::class));
        $this->assertFalse(Primitives::isPrimitive(Primitives::class));
    }

    public function testIsPrimitiveWithNonExistentClass(): void
    {
        $this->assertFalse(Primitives::isPrimitive('NonExistentClass'));
    }

    public function testGetMetadata(): void
    {
        $metadata = Primitives::getMetadata(Fqcn::class);

        $this->assertTrue(Type::STRING === $metadata->type);
        $this->assertTrue(Fqcn::class === (string) $metadata->fqcn);
        $this->assertTrue('Fully qualified class name' === (string) $metadata->name);
        $this->assertTrue('Acme\\Property\\Email' === (string) $metadata->example);
        $this->assertTrue("The primitive's FQCN" === (string) $metadata->description);
    }

    public function testValueOfWithValidPrimitives(): void
    {
        $userId = new UserId(123);
        $email = new Email('test@example.com');
        $money = new Money(99.99);
        $complex = new ComplexObject(['key' => 'value']);

        $this->assertEquals(123, Primitives::valueOf($userId));
        $this->assertEquals('test@example.com', Primitives::valueOf($email));
        $this->assertEquals(99.99, Primitives::valueOf($money));
        $this->assertEquals(['key' => 'value'], Primitives::valueOf($complex));
    }

    public function testValueOfWithInvalidObject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NotPrimitive is not a domain primitive');
        
        Primitives::valueOf(new NotPrimitive('John', 30));
    }

    public function testCreate(): void
    {
        $userId = Primitives::create(UserId::class, 123);
        $email = Primitives::create(Email::class, 'test@example.com');
        $money = Primitives::create(Money::class, 99.99);

        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertInstanceOf(Email::class, $email);
        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(123, Primitives::valueOf($userId));
        $this->assertEquals('test@example.com', Primitives::valueOf($email));
        $this->assertEquals(99.99, Primitives::valueOf($money));
    }

    public function testCreateWithInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NotPrimitive is not a domain primitive');
        
        Primitives::create(NotPrimitive::class, 'value');
    }

    public function testCreateWithNonExistentClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NonExistentClass is not a domain primitive');
        
        Primitives::create('NonExistentClass', 'value');
    }

    public function testEquals(): void
    {
        $userId1 = new UserId(123);
        $userId2 = new UserId(123);
        $userId3 = new UserId(456);
        $email = new Email('test@example.com');

        $this->assertTrue(Primitives::equals($userId1, $userId2));
        $this->assertFalse(Primitives::equals($userId1, $userId3));
        $this->assertFalse(Primitives::equals($userId1, $email));
    }

    public function testEqualsWithInvalidObjects(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        Primitives::equals(new UserId(123), new NotPrimitive('John', 30));
    }

    public function testDifferentValueTypes(): void
    {
        $intPrimitive = new UserId(123);
        $stringPrimitive = new Email('test@example.com');
        $floatPrimitive = new Money(99.99);
        $arrayPrimitive = new ComplexObject(['key' => 'value']);

        $this->assertIsInt(Primitives::valueOf($intPrimitive));
        $this->assertIsString(Primitives::valueOf($stringPrimitive));
        $this->assertIsFloat(Primitives::valueOf($floatPrimitive));
        $this->assertIsArray(Primitives::valueOf($arrayPrimitive));
    }
}

/**
 * Test classes for domain primitives
 */
class UserId
{
    public function __construct(public int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('User ID must be positive');
        }
    }
}

#[Name('Email')]
#[Example('fasano.nm@gmail.com')]
#[Description("The user's email")]
class Email
{
    public function __construct(public string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }
}

class Money
{
    public function __construct(public float $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Money cannot be negative');
        }
    }
}

class ComplexObject
{
    public function __construct(public array $value)
    {
    }
}

/**
 * Test classes that are NOT domain primitives
 */
class NotPrimitive
{
    public function __construct(public string $name, public int $age)
    {
    }
}

class WrongPropertyName
{
    public function __construct(public string $data)
    {
    }
}

class MultipleProperties
{
    public function __construct(public string $value, public int $id)
    {
    }
}

class NoConstructor
{
    public string $value = 'test';
}

class EmptyConstructor
{
    public function __construct()
    {
        $this->value = 'test';
    }
    
    public string $value;
}

class WrongParameterName
{
    public function __construct(public string $data)
    {
    }
}

class PrivateValue
{
    public function __construct(private string $value)
    {
    }
}