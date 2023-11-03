# CakeDecimal plugin documentation

## Setup

To enable this for all your decimal columns, use this in bootstrap:
```php
\Cake\Database\TypeFactory::map(
    'decimal',
    'CakeDecimal\Database\Type\DecimalObjectType',
);
 ```

This will automatically replace the core behavior and map any incoming value to the value object on marshalling,
and also convert your database values to it when reading.

If you just want to map certain fields, you need to use an alias for those.
```php
\Cake\Database\TypeFactory::map(
    'decimal_object',
    'CakeDecimal\Database\Type\DecimalObjectType',
);
 ```
Then inside your Table classes set them explicitly inside `getSchema()`:
```php
/**
 * @return \Cake\Database\Schema\TableSchemaInterface
 */
public function getSchema(): TableSchemaInterface {
    $schema = parent::getSchema();
    $schema->setColumnType('amount', 'decimal_object');
    ...

    return $schema;
}
```

## Usage
For details on `Decimal` class, see [Decimal value object documentation](https://github.com/php-collective/decimal-object/tree/master/docs).


## Configuration

You can configure the Type class in your bootstrap.

To enable auto trim:
```php
\Cake\Database\TypeFactory::build('decimal')
    ->useAutoTrim();
```

To enable localization parsing:
```php
\Cake\Database\TypeFactory::build('decimal')
    ->useLocaleParser();
```

## Customization

You can extend the value object and use the same config as shown above to enable your custom Decimal VO extension class.
Your extension can be more strict or less strict.