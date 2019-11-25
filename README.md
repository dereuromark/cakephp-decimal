# CakePHP Decimal Plugin

[![Build Status](https://api.travis-ci.org/dereuromark/cakephp-decimal.svg?branch=master)](https://travis-ci.org/dereuromark/cakephp-decimal)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-decimal/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![License](https://poser.pugx.org/dereuromark/cakephp-decimal/license)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)

This is an alternative to
 * the core one (using float in 3.x and string in 4.x)
 * and Shim plugin (using string in 3.x just like 4.x will).

As value object you have a few advantages, especially on handling the values inside your business logic.

This branch is for use with **CakePHP 4.0+**. See [version map](https://github.com/dereuromark/cakephp-decimal/wiki#cakephp-version-map) for details.

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
 * @param \Cake\Database\Schema\TableSchema $schema
 *
 * @return \Cake\Database\Schema\TableSchema
 */
protected function _initializeSchema(TableSchema $schema) {
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
