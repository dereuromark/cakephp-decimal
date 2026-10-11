# Forms and validation

Request values normally arrive as strings. CakePHP validates those values before the plugin converts them to Decimal objects during marshalling.

```php
$orders = $this->fetchTable('Orders');
$order = $orders->patchEntity($order, $this->request->getData());

if (!$order->hasErrors()) {
    $orders->saveOrFail($order);
}
```

Keep your application's decimal, required-field and range validation. Conversion does not replace validation: invalid unlocalized input becomes `null`, and empty input also becomes `null`. A required database column can reject that value later.

If passing a Decimal object into validated entity data, cast it first so validators receive their usual input:

```php
$data['total'] = (string)$total;
$order = $orders->patchEntity($order, $data);
```

Locale parsing is optional; see [Configuration](../reference/configuration). Your validators must accept the input notation too. Enabling conversion does not make a canonical-decimal validator accept commas or grouping separators. With locale parsing enabled, canonical strings can be misinterpreted: `19.90` becomes `1990` under `de_DE`. The object-to-string example above is for the default parser; use separate conversion paths for canonical and localized data.

Changing the type does not add entity property annotations. If you use IdeHelper, add a type map to your application configuration:

```php
return [
    'IdeHelper' => [
        'typeMap' => [
            'decimal' => '\\PhpCollective\\DecimalObject\\Decimal',
        ],
    ],
];
```

Use `decimal_object` as the key when mapping selected columns under that alias.
