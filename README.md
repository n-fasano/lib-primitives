# PrimitivesLib

A PHP library for working with domain primitives that provides utilities for validation, metadata extraction, and manipulation of value objects following a specific interface pattern.

## What are Domain Primitives?

Domain primitives are simple value objects that wrap primitive types (string, int, float, etc.) with domain-specific validation and meaning. They help prevent primitive obsession and make your code more expressive and type-safe.

For example, instead of passing around raw strings for email addresses:
```php
function sendEmail(string $email) // Requires checks
```

You can use domain primitives:
```php
function sendEmail(Email $email) // Trusted input
```

## Installation

```bash
composer require fasano/lib-primitives
```

## Requirements

- Tested with PHP 8.4 only.

## Domain Primitive Interface

This library works with classes that follow a specific implicit interface:

1. **Single public property** named `value` of a builtin PHP type
2. **Constructor** that accepts the value as the first parameter named `value`
3. **Validation** logic in the constructor (optional but recommended)

### Example Domain Primitive

```php
readonly class Email
{
    public function __construct(public string $value)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid email', $value));
        }
    }
}
```

### With Metadata Attributes

You can enhance your primitives with metadata using the provided attributes:

```php
use Fasano\PrimitivesLib\Metadata\Attribute\Name;
use Fasano\PrimitivesLib\Metadata\Attribute\Example;
use Fasano\PrimitivesLib\Metadata\Attribute\Description;

#[Name('Email Address')]
#[Example('user@example.com')]
#[Description('A valid email address for user communication')]
class Email
{
    public function __construct(public string $value)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid email', $value));
        }
    }
}
```

### Using StringPrimitive Base Class

For string-based primitives, a base class is provided that can be used for inspiration:

```php
use Fasano\PrimitivesLib\StringPrimitive;

class ProductCode extends StringPrimitive
{
    public static function check(string $value): bool
    {
        return preg_match('/^[A-Z]{2}-\d{4}$/', $value) === 1;
    }
}

// Usage
$code = new ProductCode('AB-1234'); // Valid
$code = new ProductCode('invalid'); // Throws InvalidArgumentException
```

## Examples

```php
use Fasano\PrimitivesLib\Primitives;

$isPrimitive = Primitives::isPrimitive(Email::class); // true
$isPrimitive = Primitives::isPrimitive(stdClass::class); // false
```

```php
$metadata = Primitives::getMetadata(Email::class);

echo $metadata->name;        // "Email Address"
echo $metadata->example;     // "user@example.com"
echo $metadata->description; // "A valid email address for user communication"
echo $metadata->type; // "string"
echo $metadata->fqcn;        // "MyApp\Domain\User\Property\Email"
```

```php
$email = new Email('user@example.com');
$value = Primitives::valueOf($email); // "user@example.com"
```

```php
$email = Primitives::create(Email::class, 'user@example.com');
// Equivalent to: new Email('user@example.com')
```

```php
$email1 = new Email('user@example.com');
$email2 = new Email('user@example.com');
$email3 = new Email('other@example.com');

Primitives::equals($email1, $email2); // true
Primitives::equals($email1, $email3); // false
```

## Error Handling

The library throws `InvalidArgumentException` when trying to use a non-primitive class with any of the utility methods.

## License

This project is licensed under the MIT License.