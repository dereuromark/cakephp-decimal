# CakePHP Decimal Plugin

[![License](https://poser.pugx.org/dereuromark/cakephp-decimal/license)](https://packagist.org/packages/dereuromark/cakephp-decimal)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)

This is an alternative to the core one (using float in 3.x and string in 4.x)
 * and Shim plugin (using string in 3.x just like 4.x will).
 * As value object you have a few advantages, especially on handling the values inside your business logic.
 
 This branch is for use with **CakePHP 3.7+**.
 
## Requirements 
 
 - Uses [spryker/decimal](https://github.com/spryker/decimal) and as such requires bcmath extension.
 
## Installation
Require the plugin through Composer:
```
composer require dereuromark/cakephp-decimal:dev-master
```

Also for now (needed for the above command):
```
"repositories": [
    {
        "type": "git",
        "url": "git@github.com:dereuromark/cakephp-decimal.git"
    }
],
```

## Usage

To enable this for all your decimal columns, use this in bootstrap:
```php
Type::map('decimal', 'CakeDecimal\Database\Type\DecimalType');
 ```

This will automatically replace the core behavior and map any incoming value to the value object on marshalling, 
and also convert your database values to it when reading.

If you just want to map certain fields, you need to use an alias for those.
```php
Type::map('decimal_object', 'CakeDecimal\Database\Type\DecimalType');
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

## Customization

You can extend the value object and use the same config as shown above to enable your custom Decimal VO extension class.
Your extension can be more strict or less strict.