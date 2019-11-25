<?php

namespace CakeDecimal\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class DecimalTypesFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id' => ['type' => 'integer'],
		'name' => ['type' => 'string', 'null' => true],
		'amount_required' => ['type' => 'decimal', 'length' => 10, 'precision' => 6, 'null' => false],
		'amount_nullable' => ['type' => 'decimal', 'length' => 10, 'precision' => 6, 'null' => true],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
	];

	/**
	 * Records property
	 *
	 * @var array
	 */
	public $records = [
		[
			'name' => 'Something',
			'amount_required' => '20.123',
			'amount_nullable' => null,
		],
	];

}
