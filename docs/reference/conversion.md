# Conversion behavior

| Method | Behavior |
| --- | --- |
| `marshal()` | Converts numeric request input or a Decimal object; returns `null` for empty or invalid unlocalized input. Optional locale parsing handles string input separately. |
| `toPHP()` | Converts non-null database values to Decimal objects; optional auto trim applies here. |
| `manyToPHP()` | Applies `toPHP()` to selected, non-null fields and leaves other values unchanged. |
| `toDatabase()` | Returns `null` for null or empty input; converts other input to Decimal when needed. |
| `toStatement()` | Uses PDO string binding. |

The database enforces its own decimal precision and scale. The plugin does not add rounding rules, migrations or monetary currencies. SQLite's numeric storage can convert decimal values to numbers and does not offer the same exact decimal storage as MySQL or PostgreSQL.

The value object is immutable. Arithmetic produces a new object; it does not modify the original. Use decimal strings to avoid introducing float error before conversion.

See the [Decimal object documentation](https://github.com/php-collective/decimal-object/tree/master/docs) for arithmetic, rounding and comparison APIs.
