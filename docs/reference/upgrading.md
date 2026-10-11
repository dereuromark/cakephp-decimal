# Upgrading

Review the installed version's Composer constraints before upgrading CakePHP or PHP. The current code targets CakePHP 5; CakePHP 6 compatibility requires testing against that release.

Older documentation claimed that the Number helper avoided floating-point formatting and that a configurable custom value-object class was supported. The helper delegates to CakePHP's number formatter, and the type directly constructs the standard Decimal class. Review code that depended on either claim.

If replacing a core decimal string mapping, update code that assumes entity values are strings. Cast explicitly when passing values to validators or APIs that require strings. Apply entity type annotations and review serializers, JSON responses and templates.


The unreleased locale-parser fix rejects malformed grouping instead of stripping every separator. For example, `19.90` returns `null` under `de_DE`; submit `19,90` for that locale. Grouping follows the locale, including secondary group sizes such as Indian numbering. Unparseable input and numeric prefixes with trailing invalid characters return `null` instead of throwing or accepting only the prefix. Localized string conversion no longer falls back to floating-point parsing. Scientific notation in request strings is rejected to prevent exponent expansion from exhausting memory; supply plain decimal strings in form and API payloads.

## Decimal object 2.0

The plugin works with `php-collective/decimal-object` 1.x and 2.x. The type itself needs no change in your application: floats coming from a driver or from entity data are converted as before.

Your own code that calculates with the value objects is affected by 2.0, because it no longer drops digits without being told how:

- `divide()` needs a rounding mode when the quotient does not fit the scale, for example `$total->divide(3, 2, Decimal::ROUND_HALF_UP)`.
- `Decimal::create()` and the arithmetic methods reject floats. Pass strings, or use `Decimal::createFromFloat()`.
- `Decimal::create($value, $scale)` throws when digits would be lost and pads the string to the scale otherwise.

Read the [Decimal object upgrade guide](https://php-collective.github.io/decimal-object/reference/upgrading) before raising the version.
