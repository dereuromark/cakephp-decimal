# Formatting

Load the plugin's Number helper under the standard alias in `AppView::initialize()`:

```php
$this->loadHelper('Number', ['className' => 'CakeDecimal.Number']);
```

In a template:

```php
<?= $this->Number->format($order->total) ?>
<?= $this->Number->currency($order->total, 'EUR') ?>
```

The helper accepts Decimal objects in `format()`, `currency()`, `formatDelta()`, `precision()` and `toPercentage()`. For `format()`, `currency()` and `formatDelta()`, the object's scale supplies the default `places` option. Set `places` explicitly to override it. `precision()` uses its precision argument.

Null values produce an empty string by default. An options array can supply `default` for a different result.

These methods delegate to CakePHP's number formatting, which converts values to floats. They can round or lose significant digits for large or very precise decimals. Use the decimal string representation when exact digits must be preserved; do calculations with Decimal objects.

For a Decimal input, `toPercentage()` defaults to multiplying by 100: `0.123` displays as `12.30%`. Pass `['multiply' => false]` when your value already represents a percentage.

If you have an application Number helper, extend CakePHP's helper and add the trait:

```php
namespace App\View\Helper;

use Cake\View\Helper\NumberHelper as CoreNumberHelper;
use CakeDecimal\View\Helper\NumberHelperTrait;

class NumberHelper extends CoreNumberHelper {
    use NumberHelperTrait;
}
```

The trait overrides the five methods listed above. Its methods call their parent implementations.
