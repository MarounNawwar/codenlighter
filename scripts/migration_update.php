<?php

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Application;

// Load your EntityManager
$entityManager = require __DIR__ . '/config/doctrine.php';

// Load migration configuration
$migrationConfig = new PhpFile(__DIR__ . '/conf/migrations.php');
$dependencyFactory = DependencyFactory::fromEntityManager($migrationConfig, function () use ($entityManager) {
    return $entityManager;
});

// Create Symfony Console Application
$application = new Application();
$application->setAutoExit(false);
ConsoleRunner::addCommands($application, $dependencyFactory);

// Execute the migration
$input = new ArrayInput([
    'command' => 'migrations:migrate'
]);
$output = new ConsoleOutput();

$application->run($input, $output);
