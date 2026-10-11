<?php declare(strict_types=1);

namespace CakeDecimal\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type\BaseType;
use Cake\Database\Type\BatchCastingInterface;
use IntlChar;
use NumberFormatter;
use PDO;
use PhpCollective\DecimalObject\Decimal;
use RuntimeException;

/**
 * Decimal type converter using value object.
 *
 * Converts database decimals and request input to Decimal value objects.
 *
 * @link https://github.com/php-collective/decimal-object
 */
class DecimalObjectType extends BaseType implements BatchCastingInterface {

	/**
	 * The class to use for representing number objects
	 *
	 * @var string
	 */
	public static $numberClass = 'Cake\I18n\Number';

	/**
	 * Set to true to have auto-trimmed values coming from DB.
	 * This makes form handling a bit easier, as it only outputs actual values of interest here.
	 *
	 * @var bool
	 */
	protected $_autoTrim = false;

	/**
	 * Whether numbers should be parsed using a locale aware parser
	 * when marshalling string inputs.
	 *
	 * @var bool
	 */
	protected $_useLocaleParser = false;

	/**
	 * Convert decimal data into the database format.
	 *
	 * @param \PhpCollective\DecimalObject\Decimal|string|float|int|null $value The value to convert.
	 * @param \Cake\Database\Driver $driver The driver instance to convert with.
	 * @throws \InvalidArgumentException
	 * @return \PhpCollective\DecimalObject\Decimal|null
	 */
	public function toDatabase(mixed $value, Driver $driver): mixed {
		if ($value === null || $value === '') {
			return null;
		}

		if (!($value instanceof Decimal)) {
			$value = Decimal::create($value);
		}

		return $value;
	}

	/**
	 * Convert database values to Decimal objects.
	 *
	 * @param string|float|int|null $value The value to convert.
	 * @param \Cake\Database\Driver $driver The driver instance to convert with.
	 * @return \PhpCollective\DecimalObject\Decimal|null
	 */
	public function toPHP(mixed $value, Driver $driver): mixed {
		if ($value === null) {
			return $value;
		}

		$decimal = Decimal::create($value);
		if ($this->_autoTrim) {
			return $decimal->trim();
		}

		return $decimal;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array
	 */
	public function manyToPHP(array $values, array $fields, Driver $driver): array {
		foreach ($fields as $field) {
			if (!isset($values[$field])) {
				continue;
			}

			$values[$field] = $this->toPHP($values[$field], $driver);
		}

		return $values;
	}

	/**
	 * Get the PDO binding type for decimal data.
	 *
	 * @param mixed $value The value being bound.
	 * @param \Cake\Database\Driver $driver The driver.
	 * @return int
	 */
	public function toStatement($value, Driver $driver): int {
		return PDO::PARAM_STR;
	}

	/**
	 * Marshals request data into PHP Decimal value objects.
	 *
	 * @param mixed $value The value to convert.
	 * @return \PhpCollective\DecimalObject\Decimal|null Converted value.
	 */
	public function marshal(mixed $value): mixed {
		if ($value === null || $value === '') {
			return null;
		}
		if (is_string($value) && $this->_useLocaleParser) {
			return $this->_parseValue($value);
		}
		if (is_numeric($value)) {
			return Decimal::create($value);
		}
		if (is_string($value) && preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
			return Decimal::create($value);
		}
		if ($value instanceof Decimal) {
			return $value;
		}

		return null;
	}

	/**
	 * @param bool $enable
	 * @return void
	 */
	public function useAutoTrim(bool $enable = true): void {
		$this->_autoTrim = $enable;
	}

	/**
	 * Sets whether or not to parse numbers passed to the marshal() function
	 * by using a locale aware parser.
	 *
	 * @param bool $enable Whether or not to enable
	 * @throws \RuntimeException
	 * @return $this
	 */
	public function useLocaleParser(bool $enable = true) {
		if ($enable === false) {
			$this->_useLocaleParser = $enable;

			return $this;
		}
		if (static::$numberClass === 'Cake\I18n\Number' ||
			is_subclass_of(static::$numberClass, 'Cake\I18n\Number')
		) {
			$this->_useLocaleParser = $enable;

			return $this;
		}

		throw new RuntimeException(
			sprintf('Cannot use locale parsing with the %s class', static::$numberClass),
		);
	}

	/**
	 * Converts a locale-formatted string into a Decimal value object.
	 *
	 * Locale parsing is performed without a float intermediate so the full
	 * precision of the input string is preserved. The active locale's
	 * grouping separator is stripped and its decimal separator is replaced
	 * with `.` so the resulting canonical numeric string can be passed
	 * directly to Decimal::create().
	 *
	 * @param string $value The value to parse and convert to a Decimal.
	 * @return \PhpCollective\DecimalObject\Decimal|null
	 */
	protected function _parseValue(string $value): ?Decimal {
		/** @var \Cake\I18n\Number $class */
		$class = static::$numberClass;
		$formatter = $class::formatter();

		$groupingSeparator = $formatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$decimalSeparator = $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

		$canonical = trim($value);
		$canonical = trim(preg_replace('/[\x{061C}\x{200E}\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $canonical) ?? $canonical);
		$canonical = str_replace('−', '-', $canonical);
		$canonical = preg_replace_callback(
			'/\p{Nd}/u',
			static fn (array $match): string => (string)IntlChar::digit($match[0]),
			$canonical,
		) ?? $canonical;
		$localized = $canonical;
		if ($groupingSeparator !== '' && str_contains($canonical, $groupingSeparator)) {
			$parts = $decimalSeparator !== '' ? explode($decimalSeparator, $canonical) : [$canonical];
			if (count($parts) > 2 || (isset($parts[1]) && str_contains($parts[1], $groupingSeparator))) {
				return null;
			}

			$groups = explode($groupingSeparator, ltrim($parts[0], '+-'));
			$primarySize = (int)$formatter->getAttribute(NumberFormatter::GROUPING_SIZE);
			$secondarySize = (int)$formatter->getAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE);
			$secondarySize = $secondarySize ?: $primarySize;
			$lastGroup = array_pop($groups);
			$firstGroup = array_shift($groups);
			if ($primarySize < 1 ||
				!ctype_digit($lastGroup) || strlen($lastGroup) !== $primarySize ||
				!ctype_digit($firstGroup) || $firstGroup[0] === '0' || strlen($firstGroup) > $secondarySize
			) {
				return null;
			}
			foreach ($groups as $group) {
				if (!ctype_digit($group) || strlen($group) !== $secondarySize) {
					return null;
				}
			}
		}
		if ($groupingSeparator !== '') {
			$canonical = str_replace($groupingSeparator, '', $canonical);
		}
		if ($decimalSeparator !== '' && $decimalSeparator !== '.') {
			$canonical = str_replace($decimalSeparator, '.', $canonical);
		}
		$canonical = trim($canonical);

		if ($canonical === '' || !is_numeric($canonical)) {
			// Fall back to NumberFormatter parsing when canonicalization fails
			// (e.g. exotic locales). This can lose precision; only accept
			// the fallback when the full input was parsed.
			$position = 0;
			$parsed = $formatter->parse($localized, NumberFormatter::TYPE_DOUBLE, $position);
			// PHP 8.4 switched formatter offsets from UTF-16 units to UTF-8 bytes.
			$length = PHP_VERSION_ID >= 80400
				? strlen($localized)
				: intdiv(strlen(mb_convert_encoding($localized, 'UTF-16LE', 'UTF-8')), 2);
			if ($parsed === false || $position !== $length) {
				return null;
			}

			return Decimal::create($parsed);
		}

		return Decimal::create($canonical);
	}

}
