<?php

namespace NetteAddons;

use Nette\Application\Routers\Route;
use Nette\Config\Configurator;

require_once LIBS_DIR . '/autoload.php';



$configurator = new Configurator;

// Enable Debugger
$configurator->setDebugMode(TRUE);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->register();

// Create Dependency Injection container
$configurator->addConfig(__DIR__ . '/config/config.neon', Configurator::NONE);
$configurator->addConfig(__DIR__ . '/config/config.local.neon', Configurator::NONE);
$container = $configurator->createContainer();

// Setup router
$container->router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('packages.json', 'Packages:default');
$container->router[] = new Route('<presenter>[/<action>]', 'Homepage:default');

// Run the application!
if (!$container->parameters['consoleMode']) {
	$container->application->run();
}
