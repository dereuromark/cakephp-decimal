<?php

namespace CakeDecimal\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class FloatTypesFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = [
		'id' => ['type' => 'integer'],
		'name' => ['type' => 'string', 'null' => true],
		'amount_required' => ['type' => 'float', 'null' => false],
		'amount_nullable' => ['type' => 'float', 'null' => true],
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
			'amount_required' => 20.123,
			'amount_nullable' => null,
		],
	];

}
