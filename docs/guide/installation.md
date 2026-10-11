# Installation

This development branch requires CakePHP 6 and PHP 8.5. Run these commands
in your application directory:

```sh
composer config minimum-stability dev
composer config prefer-stable true
composer require cakephp/cakephp:^6.0@dev dereuromark/cakephp-decimal:dev-cake6
```

Composer checks the supported PHP and CakePHP versions and the required extensions. Enable `ext-bcmath` in both your CLI and web PHP environments.

Load the plugin from `src/Application.php`, inside `bootstrap()` after the parent call:

```php
$this->addPlugin('CakeDecimal');
```

The bootstrap maps CakePHP's `decimal` database type to `DecimalObjectType`. This applies to all decimal columns discovered by the ORM. The plugin creates no database tables and needs no migrations of its own.

Continue with [Getting started](./getting-started). To keep the core string type for most columns, use [Selected columns](./selected-columns) instead.
