# Configuration

Configure the type after its mapping is registered. One explicit setup in `Application::bootstrap()`, after the parent call, disables automatic plugin bootstrap and registers the mapping before setting options:

```php
use Cake\Database\TypeFactory;
use CakeDecimal\Database\Type\DecimalObjectType;

$this->addPlugin('CakeDecimal', ['bootstrap' => false]);
TypeFactory::map('decimal', DecimalObjectType::class);
$type = TypeFactory::build('decimal');
assert($type instanceof DecimalObjectType);
$type->useAutoTrim();
$type->useLocaleParser();
```

Calling `addPlugin()` alone does not immediately run plugin bootstrap. Building the type before its mapping is registered would configure the core type instead.

Both options are disabled by default. Each accepts `false` to disable it again. Use your registered alias instead of `decimal` when configuring selected columns.

`useAutoTrim()` removes trailing fractional zeros when converting database values to PHP. It does not trim request values in `marshal()` or change the database column scale. It returns `void` and cannot be chained.

`useLocaleParser()` enables locale-aware conversion of string request values. It uses the active CakePHP locale and returns the type instance. It does not localize stored database values or configure form validation.

```php
use Cake\I18n\I18n;

I18n::setLocale('de_DE');
$value = $type->marshal('1.234,50');
echo (string)$value; // 1234.50
```

The parser removes the locale's grouping separator and replaces its decimal separator without using a float for ordinary numeric input. For input that cannot be normalized, it falls back to NumberFormatter parsing, which can lose precision. Invalid input can throw a Decimal exception rather than return `null` on this path. Validate user input before conversion.

The public static `DecimalObjectType::$numberClass` defaults to `Cake\I18n\Number`. Locale parsing only supports that class or a subclass. This setting selects the formatter, not a custom Decimal value-object class: conversion always uses `PhpCollective\DecimalObject\Decimal`.
