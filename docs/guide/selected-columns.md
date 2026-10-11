# Selected columns

Disable plugin bootstrap if you want to keep CakePHP's core decimal mapping:

```php
// Inside Application::bootstrap(), after parent::bootstrap().
$this->addPlugin('CakeDecimal', ['bootstrap' => false]);
```

Register a separate type alias in `config/bootstrap.php`:

```php
use Cake\Database\TypeFactory;
use CakeDecimal\Database\Type\DecimalObjectType;

TypeFactory::map('decimal_object', DecimalObjectType::class);
```

Set the alias in the table's `initialize()` method:

```php
public function initialize(array $config): void {
    parent::initialize($config);

    $this->getSchema()->setColumnType('total', 'decimal_object');
}
```

This changes ORM conversion for `total`; it does not alter the database column. Other decimal columns continue using the core type.

Apply options to `TypeFactory::build('decimal_object')` when using this alias. Configuring `decimal` would configure the core type instead.
