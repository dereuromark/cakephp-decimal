# CakePHP Decimal Plugin

[![CI](https://github.com/dereuromark/cakephp-decimal/workflows/CI/badge.svg?branch=master)](https://travis-ci.org/dereuromark/cakephp-decimal)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-decimal/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![codecov](https://codecov.io/gh/dereuromark/cakephp-decimal/branch/master/graph/badge.svg)](https://codecov.io/gh/dereuromark/cakephp-decimal)
[![License](https://poser.pugx.org/dereuromark/cakephp-decimal/license)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

This is an alternative to
 * the core Decimal type (using plain strings)

As value object you have a few advantages, especially on handling the values inside your business logic.

This branch is for use with **CakePHP 4.2+**. See [version map](https://github.com/dereuromark/cakephp-decimal/wiki#cakephp-version-map) for details.

## Requirements

- Uses [spryker/decimal-object](https://github.com/spryker/decimal-object) and as such requires bcmath extension.

## Installation
Require the plugin through Composer:
```
composer require dereuromark/cakephp-decimal
```

## Usage

To enable this for all your decimal columns, use this in bootstrap:
```php
Type::map('decimal', 'CakeDecimal\Database\Type\DecimalObjectType');
 ```

This will automatically replace the core behavior and map any incoming value to the value object on marshalling,
and also convert your database values to it when reading.

If you just want to map certain fields, you need to use an alias for those.
```php
Type::map('decimal_object', 'CakeDecimal\Database\Type\DecimalObjectType');
 ```
Then inside your Table classes set them explicitly inside `_initializeSchema()`:
```php
/**
 * @param \Cake\Database\Schema\TableSchemaInterface $schema
 *
 * @return \Cake\Database\Schema\TableSchemaInterface
 */
protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface {
    $schema->setColumnType('amount', 'decimal_object');
    ...

    return $schema;
}
```

For details on `Decimal` class, see [Decimal value object documentation](https://github.com/spryker/decimal-object/tree/master/docs).


## Configuration

You can configure the Type class in your bootstrap.

To enable auto trim:
```php
Type::build('decimal')
    ->useAutoTrim();
```

To enable localization parsing:
```php
Type::build('decimal')
    ->useLocaleParser();
```

## Customization

You can extend the value object and use the same config as shown above to enable your custom Decimal VO extension class.
Your extension can be more strict or less strict.
