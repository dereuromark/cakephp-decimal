<?php

namespace CakeDecimal\Test\TestCase\Database\Type;

use Cake\Database\Driver;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CakeDecimal\Database\Type\DecimalObjectType;
use PhpCollective\DecimalObject\Decimal;
use TestApp\Model\Table\DecimalTypesTable;
use TestApp\Model\Table\FloatTypesTable;

/**
 * @link https://php-decimal.io/
 * @see https://github.com/cakephp/cakephp/pull/13086
 */
class DecimalObjectTypeTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.CakeDecimal.DecimalTypes',
		'plugin.CakeDecimal.FloatTypes',
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

		TypeFactory::map('decimal', DecimalObjectType::class);

		$this->Table = TableRegistry::getTableLocator()->get('DecimalTypes', ['className' => DecimalTypesTable::class]);
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
	public function testSave(): void {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => '-1.11',
			'amount_nullable' => null,
		]);
		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);

		$this->assertInstanceOf(Decimal::class, $record->amount_required);
		$this->assertNull($record->amount_nullable);

		if ($this->isConnection('Sqlite')) {
			$this->assertSame('-1.11', (string)$record->amount_required);
		} else {
			$this->assertSame('-1.110000', (string)$record->amount_required);
		}
	}

	/**
	 * @return void
	 */
	public function testSaveObject(): void {
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

		$this->_needsConnection('Mysql');

		// Now it has the precision/scale of DB
		$this->assertSame('0.000001', (string)$record->amount_required);
		$this->assertSame('-1.110000', (string)$record->amount_nullable);
	}

	/**
	 * @return void
	 */
	public function testMarshalInvalid(): void {
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
	public function testMarshalEmptyString(): void {
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
	public function testPrecisionDecimal(): void {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => '0.000000000000000000000000000001',
			'amount_nullable' => '0.000000000000000000000000000002',
		]);
		$this->Table->saveOrFail($record);

		/** @var \PhpCollective\DecimalObject\Decimal $decimal */
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
	public function testPrecisionFloat(): void {
		$this->Table = TableRegistry::getTableLocator()->get('FloatTypes', ['className' => FloatTypesTable::class]);

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
	public function testPrecisionDecimalExtended(): void {
		$record = $this->Table->newEntity([
			'name' => 'Foo',
			'amount_required' => 4 / 3,
			'amount_nullable' => 7 / 3,
		]);
		$this->Table->saveOrFail($record);

		/** @var \PhpCollective\DecimalObject\Decimal $decimal */
		$decimal = $record->amount_nullable;
		$newDecimal = $record->amount_required->add($record->amount_nullable);
		$record->amount_nullable = $newDecimal;

		$this->Table->saveOrFail($record);

		$record = $this->Table->get($record->id);
		if ($this->isConnection('Sqlite')) {
			$this->assertSame('3.6666666666666', (string)$record->amount_nullable);
		} else {
			$this->assertSame('3.666667', (string)$record->amount_nullable);
		}

		// Directly doing math on the floats reveals the precision issues
		$this->assertSame('3.6666666666667', (string)Decimal::create(4 / 3 + 7 / 3));
	}

	/**
	 * Compare handling of float to above Decimal usage.
	 *
	 * @return void
	 */
	public function testPrecisionFloatExtended(): void {

		$this->Table = TableRegistry::getTableLocator()->get('FloatTypes', ['className' => FloatTypesTable::class]);

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

		// Note the last digit being rounded up
		if ($this->isConnection('Sqlite') || $this->isConnection('Postgres')) {
			$this->assertSame(3.6666666666667, $record->amount_nullable);
			$this->assertSame('3.6666666666667', (string)(4 / 3 + 7 / 3));
		} else {
			$this->assertSame(3.66667, $record->amount_nullable);
			$this->assertSame('3.6666666666667', (string)(4 / 3 + 7 / 3));
		}
	}

	/**
	 * @return void
	 */
	public function testAutoTrim(): void {
		$type = new DecimalObjectType();
		$type->useAutoTrim();

		$value = '12.121000';
		$driver = $this->getMockBuilder(Driver::class)->getMock();
		$result = $type->toPHP($value, $driver);

		$this->assertSame('12.121', (string)$result);
	}

	/**
	 * @return void
	 */
	public function testUseLocalParser(): void {
		$type = new DecimalObjectType();
		$type->useLocaleParser();

		$locale = I18n::getLocale();
		I18n::setLocale('de_DE');
		$value = '12,1210';
		$result = $type->marshal($value);
		I18n::setLocale($locale);

		$this->assertSame('12.121', (string)$result);
	}
	/**
	 * @return void
	 */
	public function testManyToPHP(): void {
		$type = new DecimalObjectType();
		$driver = $this->getMockBuilder(Driver::class)->getMock();

		$values = [
			'x' => 1.1,
			'y' => '2',
		];
		$fields = [
			'x',
			'y',
		];
		$result = $type->manyToPHP($values, $fields, $driver);

		$this->assertCount(2, $result);
		$this->assertSame('1.1', (string)$result['x']);
		$this->assertSame('2', (string)$result['y']);
	}

	/**
	 * Helper method for skipping tests that need a real connection.
	 *
	 * @param string|null $driver
	 * @return void
	 */
	protected function _needsConnection(?string $driver = null): void {
		if ($driver) {
			$this->skipIf(!$this->isConnection($driver), 'Not using ' . $driver . ' for test config.');

			return;
		}

		$this->skipIf($this->isConnection('SqLite'), 'Tests do not work with Sqlite.');
	}

	/**
	 * @param string $driver
	 *
	 * @return bool
	 */
	protected function isConnection(string $driver): bool {
		$config = ConnectionManager::getConfig('test');

		return strpos($config['driver'], $driver) !== false;
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
