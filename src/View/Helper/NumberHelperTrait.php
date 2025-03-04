<?php declare(strict_types=1);

namespace CakeDecimal\View\Helper;

use PhpCollective\DecimalObject\Decimal;

trait NumberHelperTrait {

	/**
	 * @param \PhpCollective\DecimalObject\Decimal|string|float|int $number
	 * @param array<string, mixed> $options
	 *
	 * @return string Formatted number
	 */
	public function format(Decimal|string|float|int $number, array $options = []): string {
		if ($number instanceof Decimal) {
			$options += ['places' => $number->scale()];
			$number = (string)$number;
		}

		return parent::format($number, $options);
	}

	/**
	 * @param \PhpCollective\DecimalObject\Decimal|string|float $number Value to format.
	 * @param string|null $currency International currency name such as 'USD', 'EUR', 'JPY', 'CAD'
	 * @param array<string, mixed> $options Options list.
	 *
	 * @return string Number formatted as a currency.
	 */
	public function currency(Decimal|string|float $number, ?string $currency = null, array $options = []): string {
		if ($number instanceof Decimal) {
			$options += ['places' => $number->scale()];
			$number = (string)$number;
		}

		return parent::currency($number, $currency, $options);
	}

	/**
	 * @param \PhpCollective\DecimalObject\Decimal|string|float $value A floating point number
	 * @param array<string, mixed> $options Options list.
	 *
	 * @return string formatted delta
	 */
	public function formatDelta(Decimal|string|float $value, array $options = []): string {
		if ($value instanceof Decimal) {
			$options += ['places' => $value->scale()];
			$value = (string)$value;
		}

		return parent::formatDelta($value, $options);
	}

	/**
	 * @param \PhpCollective\DecimalObject\Decimal|string|float|int $value
	 * @param int $precision
	 * @param array $options
	 *
	 * @return string Human readable size
	 */
	public function precision(Decimal|string|float|int $value, int $precision = 3, array $options = []): string {
		if ($value instanceof Decimal) {
			$options += ['places' => $value->scale()];
			$value = (string)$value;
		}

		return parent::precision($value, $precision, $options);
	}

	/**
	 * @param \PhpCollective\DecimalObject\Decimal|string|float|int $value A floating point number
	 * @param int $precision The precision of the returned number
	 * @param array<string, mixed> $options Options
	 *
	 * @return string Percentage string
	 */
	public function toPercentage(Decimal|string|float|int $value, int $precision = 2, array $options = []): string {
		if ($value instanceof Decimal) {
			$value = (string)$value;
			$options += ['multiply' => true];
		}

		return parent::toPercentage($value, $precision, $options);
	}

}
