<?php

namespace CakeDecimal\Database\Type;

use Cake\Database\DriverInterface;
use Cake\Database\Type\BaseType;
use Cake\Database\Type\BatchCastingInterface;
use PDO;
use RuntimeException;
use Spryker\DecimalObject\Decimal;

/**
 * Decimal type converter using value object.
 *
 * This is an alternative to the core one (using float in 3.x and string in 4.x)
 * and Shim plugin (using string in 3.x just like 4.x will).
 * As value object you have a few advantages, especially on handling the values inside your business logic.
 *
 * @link https://github.com/spryker/decimal
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
	 * @param string|int|float|\Spryker\DecimalObject\Decimal|null $value The value to convert.
	 * @param \Cake\Database\DriverInterface $driver The driver instance to convert with.
	 * @return \Spryker\DecimalObject\Decimal|null
	 * @throws \InvalidArgumentException
	 */
	public function toDatabase($value, DriverInterface $driver) {
		if ($value === null || $value === '') {
			return null;
		}

		if (!($value instanceof Decimal)) {
			if (is_object($value)
				&& method_exists($value, '__toString')
				&& is_numeric(strval($value))
			) {
				$value = strval($value);
			}

			$value = Decimal::create($value);
		}

		return $value;
	}

	/**
	 * Convert float values to PHP floats
	 *
	 * @param string|int|float|null $value The value to convert.
	 * @param \Cake\Database\DriverInterface $driver The driver instance to convert with.
	 * @return \Spryker\DecimalObject\Decimal|null
	 */
	public function toPHP($value, DriverInterface $driver) {
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
	public function manyToPHP(array $values, array $fields, DriverInterface $driver): array {
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
	 * @param \Cake\Database\DriverInterface $driver The driver.
	 * @return int
	 */
	public function toStatement($value, DriverInterface $driver): int {
		return PDO::PARAM_STR;
	}

	/**
	 * Marshals request data into PHP Decimal value objects.
	 *
	 * @param mixed $value The value to convert.
	 * @return \Spryker\DecimalObject\Decimal|null Converted value.
	 */
	public function marshal($value) {
		if ($value === null || $value === '') {
			return null;
		}
		if (is_string($value) && $this->_useLocaleParser) {
			return $this->_parseValue($value);
		}
		if (is_numeric($value)) {
			return Decimal::create($value);
		}
		if (is_string($value) && preg_match('/^[0-9,. ]+$/', $value)) {
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
	 * @return $this
	 * @throws \RuntimeException
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
			sprintf('Cannot use locale parsing with the %s class', static::$numberClass)
		);
	}

	/**
	 * Converts a string into a float point after parsing it using the locale
	 * aware parser.
	 *
	 * @param string $value The value to parse and convert to an float.
	 * @return \Spryker\DecimalObject\Decimal
	 */
	protected function _parseValue(string $value): Decimal {
		/** @var \Cake\I18n\Number $class */
		$class = static::$numberClass;

		return Decimal::create($class::parseFloat($value));
	}

}
