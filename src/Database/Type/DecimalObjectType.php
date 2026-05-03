<?php declare(strict_types=1);

namespace CakeDecimal\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type\BaseType;
use Cake\Database\Type\BatchCastingInterface;
use NumberFormatter;
use PDO;
use PhpCollective\DecimalObject\Decimal;
use RuntimeException;

/**
 * Decimal type converter using value object.
 *
 * This is an alternative to the core one (using float in 3.x and string in 4.x)
 * and Shim plugin (using string in 3.x just like 4.x will).
 * As value object you have a few advantages, especially on handling the values inside your business logic.
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
	 * Convert integer data into the database format.
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
	 * Convert float values to PHP floats
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
	 * Get the correct PDO binding type for integer data.
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
		if (is_string($value) && preg_match('/^-?[0-9]+(?:\.[0-9]+)?$/', $value)) {
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
	 * @return \PhpCollective\DecimalObject\Decimal
	 */
	protected function _parseValue(string $value): Decimal {
		/** @var \Cake\I18n\Number $class */
		$class = static::$numberClass;
		$formatter = $class::formatter();

		$groupingSeparator = $formatter->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
		$decimalSeparator = $formatter->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

		$canonical = $value;
		if ($groupingSeparator !== '') {
			$canonical = str_replace($groupingSeparator, '', $canonical);
		}
		if ($decimalSeparator !== '' && $decimalSeparator !== '.') {
			$canonical = str_replace($decimalSeparator, '.', $canonical);
		}
		$canonical = trim($canonical);

		if ($canonical === '' || !is_numeric($canonical)) {
			// Fall back to NumberFormatter parsing when canonicalization fails
			// (e.g. exotic locales). This still loses precision, but matches
			// the historical behavior for unrecognized inputs.
			return Decimal::create($class::parseFloat($value));
		}

		return Decimal::create($canonical);
	}

}
