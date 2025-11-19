<?php declare(strict_types=1);

namespace CakeDecimal;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Database\TypeFactory;
use CakeDecimal\Database\Type\DecimalObjectType;

/**
 * Plugin for CakeDecimal
 */
class CakeDecimalPlugin extends BasePlugin {

	protected bool $routesEnabled = false;

	protected bool $middlewareEnabled = false;

	protected bool $consoleEnabled = false;

	protected bool $servicesEnabled = false;

	/**
	 * @inheritDoc
	 */
	public function bootstrap(PluginApplicationInterface $app): void {
		TypeFactory::map('decimal', DecimalObjectType::class);
	}

}
