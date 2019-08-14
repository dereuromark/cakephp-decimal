<?php

namespace CakeDecimal\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use Cake\Database\Type\BatchCastingInterface;
use PDO;
use RuntimeException;
use Spryker\Decimal\Decimal;

/**
 * Decimal type converter using value object.
 *
 * Use to convert decimal data between PHP and the database types.
 */
class DecimalObjectType extends Type implements BatchCastingInterface {

	/**
	 * The class to use for representing number objects
	 *
	 * @var string
	 */
	public static $numberClass = 'Cake\I18n\Number';

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
	 * @param string|int|float|\Spryker\Decimal\Decimal|null $value The value to convert.
	 * @param \Cake\Database\Driver $driver The driver instance to convert with.
	 * @return \Spryker\Decimal\Decimal|null
	 * @throws \InvalidArgumentException
	 */
	public function toDatabase($value, Driver $driver) {
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
	 * @param string|int|float|null $value The value to convert.
	 * @param \Cake\Database\Driver $driver The driver instance to convert with.
	 * @return \Spryker\Decimal\Decimal|null
	 */
	public function toPHP($value, Driver $driver) {
		if ($value === null) {
			return $value;
		}

		return Decimal::create($value);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array
	 */
	public function manyToPHP(array $values, array $fields, Driver $driver) {
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
	public function toStatement($value, Driver $driver) {
		return PDO::PARAM_STR;
	}

	/**
	 * Marshals request data into PHP Decimal value objects.
	 *
	 * @param mixed $value The value to convert.
	 * @return \Spryker\Decimal\Decimal|null Converted value.
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
	 * Sets whether or not to parse numbers passed to the marshal() function
	 * by using a locale aware parser.
	 *
	 * @param bool $enable Whether or not to enable
	 * @return $this
	 * @throws \RuntimeException
	 */
	public function useLocaleParser($enable = true) {
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
	 * @return \Spryker\Decimal\Decimal
	 */
	protected function _parseValue($value) {
		/** @var \Cake\I18n\Number $class */
		$class = static::$numberClass;

		return Decimal::create($class::parseFloat($value));
	}

}
