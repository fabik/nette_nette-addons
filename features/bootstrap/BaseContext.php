<?php

use Behat\Behat\Context\BehatContext;

require_once __DIR__ . '/../../www/index.php';

/**
 * Base context
 *
 * @author Jan Dolecek <juzna.cz@gmail.com>
 */
abstract class BaseContext extends \Behat\MinkExtension\Context\MinkContext
{
	/** @var \SystemContainer */
	protected $context;



	function __construct(array $parameters)
	{
		global $container;
		if ( ! $container) {
			throw new \Exception("DI Container not found");
		}
		$this->context = $container;
	}

}
