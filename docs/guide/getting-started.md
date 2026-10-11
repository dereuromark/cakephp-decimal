# Getting started

After [installation](./installation), use an existing decimal column such as `Orders.total`. Store money and other exact fractional values in a database `DECIMAL` column rather than a floating-point column. Choose its precision and scale for your application.

In a controller with an `Orders` table:

```php
use PhpCollective\DecimalObject\Decimal;

$orders = $this->fetchTable('Orders');
$order = $orders->newEntity(['total' => '19.90']);

if ($order->hasErrors()) {
    throw new \RuntimeException('The order data failed validation.');
}

assert($order->total instanceof Decimal);
$order->total = $order->total->add('0.10');
$orders->saveOrFail($order);

$stored = $orders->get($order->id);
assert($stored->total instanceof Decimal);
echo (string)$stored->total;
```

Include any other required fields in your application's order data. Decimal operations return new objects, so assign the result. The example adds exactly ten cents. The database column controls the scale returned on reload; SQLite does not preserve decimal scale like MySQL or PostgreSQL.

Pass decimal strings to `Decimal::create()` and arithmetic methods. A float may have lost precision before the plugin receives it.

Use `(string)$stored->total` for the decimal representation. For localized display, read [Formatting](./formatting), including its precision limits.
