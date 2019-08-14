<?php

namespace CakeDecimal\Test\TestCase\Database\Type;

use CakeDecimal\Database\Type\DecimalObjectType;
use Cake\Database\Type;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Spryker\Decimal\Decimal;
use TestApp\Model\Table\DecimalTypesTable;

/**
 * @link https://php-decimal.io/
 * @see https://github.com/cakephp/cakephp/pull/13086
 */
class DecimalObjectTypeTest extends TestCase {

	/**
	 * @var array
	 */
	public $fixtures = [
		'plugin.CakeDecimal.DecimalTypes'
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

		$this->assertInstanceOf(Decimal::class, $record->amount_required);
		$this->assertInstanceOf(Decimal::class, $record->amount_nullable);

		$record = $this->Table->get($record->id);

		$this->assertInstanceOf(Decimal::class, $record->amount_required);
		$this->assertInstanceOf(Decimal::class, $record->amount_nullable);

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
	 * @return void
	 */
	public function testPrecisionKeeping() {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => '0.000001',
			'amount_nullable' => '0.000002',
		]);
		$this->Table->saveOrFail($record);

		/** @var \Spryker\Decimal\Decimal $decimal */
		$decimal = $record->amount_nullable;
		$newDecimal = $decimal->subtract($record->amount_required)->subtract($record->amount_required);
		$record->amount_nullable = $newDecimal;

		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);
		$this->assertTrue($record->amount_nullable->isZero());
	}

}
