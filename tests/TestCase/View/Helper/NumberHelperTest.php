<?php

namespace CakeDecimal\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use CakeDecimal\View\Helper\NumberHelper;
use PhpCollective\DecimalObject\Decimal;

class NumberHelperTest extends TestCase {

	/**
	 * @var \CakeDecimal\View\Helper\NumberHelper
	 */
	protected $Number;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->Number = new NumberHelper(new View());
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->Number);
	}

	/**
	 * @return void
	 */
	public function testFormat(): void {
		$string = '12.123';
		$result = $this->Number->format(Decimal::create($string));
		$this->assertSame($string, $result);

		$result = $this->Number->format(null);
		$this->assertSame('', $result);
	}

	/**
	 * On some systems there is a whitespace between the symbol and the value.
	 *
	 * @return void
	 */
	public function testCurrency(): void {
		$string = '12.13';

		$result = $this->Number->currency(Decimal::create($string));
		$this->assertTextContains('$', $result);
		$this->assertTextContains('12.13', $result);

		$result = $this->Number->currency(Decimal::create($string), 'EUR');
		$this->assertTextContains('€', $result);
		$this->assertTextContains('12.13', $result);

		$result = $this->Number->currency(null);
		$this->assertSame('', $result);
	}

	/**
	 * @return void
	 */
	public function testFormatDelta(): void {
		$string = '12.13';
		$result = $this->Number->formatDelta(Decimal::create($string));
		$this->assertSame('+' . $string, $result);

		$string = '-13.14';
		$result = $this->Number->formatDelta(Decimal::create($string));
		$this->assertSame($string, $result);

		$result = $this->Number->formatDelta(null);
		$this->assertSame('', $result);
	}

	/**
	 * @return void
	 */
	public function testPrecision(): void {
		$string = '1.234';
		$result = $this->Number->precision(Decimal::create($string));
		$this->assertSame('1.234', $result);

		$string = '111.23';
		$result = $this->Number->precision(Decimal::create($string));
		$this->assertSame('111.230', $result);

		$result = $this->Number->precision(null);
		$this->assertSame('', $result);
	}

	/**
	 * @return void
	 */
	public function testToPercentage(): void {
		$string = '0.123';
		$result = $this->Number->toPercentage(Decimal::create($string));
		$this->assertSame('12.30%', $result);

		$result = $this->Number->toPercentage(null);
		$this->assertSame('', $result);
	}

}
