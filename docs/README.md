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

## Templating
When using PHP templating and NumberHelper methods, you can use the extended NumberHelper that ships with this plugin.
```php
// in your AppView.php
$this->addHelper('CakeDecimal.Number');
```
This will replace the built-in core one.

If you already have a local helper, you can use the trait to add them on top:
```php
namespace App\View\Helper;

use CakeDecimal\View\Helper\NumberHelperTrait;
use Cake\View\Helper\NumberHelper as CoreNumberHelper;

class NumberHelper extends CoreNumberHelper {

	use NumberHelperTrait;

	... // other methods or replacement of trait methods

}
```

If you only need a subset or want to further customize, it can make sense to define the methods locally:
```php
namespace App\View\Helper;

use Cake\View\Helper\NumberHelper as CoreNumberHelper;
use PhpCollective\DecimalObject\Decimal;

class NumberHelper extends CoreNumberHelper {

    /**
     * @param \PhpCollective\DecimalObject\Decimal|string|float|int $number
     * @param array<string, mixed> $options
     *
     * @return string Formatted number
     */
    public function format(Decimal|string|float|int $number, array $options = []): string {
        if ($number instanceof Decimal) {
            $options += ['places' => $number->scale()];
            $number = (string)$number;
        }

        return parent::format($number, $options);
    }

    ...

}
```
See the trait for all available methods.

Pro-tip: The display of the places/precision is now also more correct compared to the default casting to float.
This is due to the Decimal value object storing the DB fields' scale internally up until string conversion on output.

## IdeHelper compatibility
For [IdeHelper plugin](https://github.com/dereuromark/cakephp-ide-helper) to automatically fix up the existing "decimal" `string` annotations to `\PhpCollective\DecimalObject\Decimal`, you should modify the `typeMap` in your `app.php` config:
```php
    'IdeHelper' => [
        ...
        'typeMap' => [
            'decimal' => '\PhpCollective\DecimalObject\Decimal',
        ],
    ],
```

## Tips

When creating objects on the fly within your code, it can be useful to have certain default lengths across your application as constants:
```php
$total = Decimal::create(ZERO_WITH_DEFAULT_PRECISION);
...
```

In your `config/constants.php` file you can define them:
```php
define('ZERO_WITH_DEFAULT_PRECISION', '0.00');
```
