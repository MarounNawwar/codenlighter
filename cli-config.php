<?php

require 'vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\JsonFile;
use Doctrine\DBAL\DriverManager;

$config = new JsonFile('./conf/migrations.json'); // Or use one of the Doctrine\Migrations\Configuration\Configuration\* loaders

$paths = [__DIR__.'/Models'];
$isDevMode = true;

$ORMConfig = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
$connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);

$entityManager = new EntityManager($connection, $ORMConfig);

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));