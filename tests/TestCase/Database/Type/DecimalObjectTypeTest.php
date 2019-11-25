<?php

namespace CakeDecimal\Test\TestCase\Database\Type;

use CakeDecimal\Database\Type\DecimalObjectType;
use Cake\Database\Type;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Spryker\DecimalObject\Decimal;
use TestApp\Model\Table\DecimalTypesTable;
use TestApp\Model\Table\FloatTypesTable;

/**
 * @link https://php-decimal.io/
 * @see https://github.com/cakephp/cakephp/pull/13086
 */
class DecimalObjectTypeTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.CakeDecimal.DecimalTypes',
		'plugin.CakeDecimal.FloatTypes'
	];

	/**
	 * @var \Cake\ORM\Table
	 */
	public $Table;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		Type::map('decimal', DecimalObjectType::class);

		$this->Table = TableRegistry::get('DecimalTypes', ['className' => DecimalTypesTable::class]);
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		unset($this->Table);
	}

	/**
	 * @return void
	 */
	public function testSave() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => '-1.11',
			'amount_nullable' => null,
		]);
		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);

		$this->assertInstanceOf(Decimal::class, $record->amount_required);
		$this->assertNull($record->amount_nullable);

		$this->assertSame('-1.11', (string)$record->amount_required);
	}

	/**
	 * @return void
	 */
	public function testSaveObject() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => Decimal::create('0.000001'),
			'amount_nullable' => Decimal::create(-1.11),
		]);
		$this->Table->saveOrFail($record);

		$this->assertSame('0.000001', (string)$record->amount_required);
		$this->assertSame('-1.11', (string)$record->amount_nullable);

		$this->assertInstanceOf(Decimal::class, $record->amount_required);
		$this->assertInstanceOf(Decimal::class, $record->amount_nullable);

		$record = $this->Table->get($record->id);

		$this->assertInstanceOf(Decimal::class, $record->amount_required);
		$this->assertInstanceOf(Decimal::class, $record->amount_nullable);

		// Now it has the precision/scale of DB
		$this->assertSame('0.000001', (string)$record->amount_required);
		$this->assertSame('-1.11', (string)$record->amount_nullable);
	}

	/**
	 * @return void
	 */
	public function testMarshalInvalid() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => 'abc',
		]);
		$this->assertNull($record->amount_required);

		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => true,
		]);
		$this->assertNull($record->amount_required);
	}

	/**
	 * @return void
	 */
	public function testMarshalEmptyString() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => '',
		]);
		$this->assertNull($record->amount_required);
	}

	/**
	 * Show precision on match operations and DB write/read.
	 *
	 * @return void
	 */
	public function testPrecisionDecimal() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => '0.000000000000000000000000000001',
			'amount_nullable' => '0.000000000000000000000000000002',
		]);
		$this->Table->saveOrFail($record);

		/** @var \Spryker\DecimalObject\Decimal $decimal */
		$decimal = $record->amount_nullable;
		$newDecimal = $decimal->subtract($record->amount_required)->subtract($record->amount_required);
		$record->amount_nullable = $newDecimal;

		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);
		$this->assertTrue($record->amount_nullable->isZero());
	}

	/**
	 * Compare handling of float to above Decimal usage.
	 *
	 * @return void
	 */
	public function testPrecisionFloat() {
		$this->Table = TableRegistry::get('FloatTypes', ['className' => FloatTypesTable::class]);

		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => 0.000000000000000000000000000001,
			'amount_nullable' => 0.000000000000000000000000000002,
		]);
		$this->Table->saveOrFail($record);

		/** @var float $float */
		$float = $record->amount_nullable;
		$newFloat = $float - ($record->amount_required + $record->amount_required);
		$record->amount_nullable = $newFloat;

		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);
		$this->assertSame(0.0, $record->amount_nullable);
	}

	/**
	 * Show precision on match operations and DB write/read.
	 *
	 * @return void
	 */
	public function testPrecisionDecimalExtended() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => 4 / 3,
			'amount_nullable' => 7 / 3,
		]);
		$this->Table->saveOrFail($record);

		/** @var \Spryker\DecimalObject\Decimal $decimal */
		$decimal = $record->amount_nullable;
		$newDecimal = $record->amount_required->add($record->amount_nullable);
		$record->amount_nullable = $newDecimal;

		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);
		$this->assertSame('3.6666666666666', (string)$record->amount_nullable);

		// Directly doing math on the floats reveals the precision issues
		$this->assertSame('3.6666666666667', (string)Decimal::create(4 / 3 + 7 / 3));
	}

	/**
	 * Compare handling of float to above Decimal usage.
	 *
	 * @return void
	 */
	public function testPrecisionFloatExtended() {
		$this->Table = TableRegistry::get('FloatTypes', ['className' => FloatTypesTable::class]);

		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => 4 / 3,
			'amount_nullable' => 7 / 3,
		]);
		$this->Table->saveOrFail($record);

		/** @var float $float */
		$float = $record->amount_nullable;
		$newFloat = $record->amount_required + $record->amount_nullable;
		$record->amount_nullable = $newFloat;

		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);
		$this->assertSame(4 / 3 + 7 / 3, $record->amount_nullable);

		// Note the last digit being rounded up
		$this->assertSame(3.6666666666667, $record->amount_nullable);
		$this->assertSame('3.6666666666667', (string)(4 / 3 + 7 / 3));
	}

	/**
	 * @return void
	 */
	public function testDivide() {
		$decimalOne = Decimal::create(1);
		$decimalTwo = Decimal::create(2);

		$decimalThree = $decimalOne->divide($decimalTwo, 10);
		$this->assertSame('0.5000000000', (string)$decimalThree);
	}

}
