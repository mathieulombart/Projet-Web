<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

require_once __DIR__ . '/vendor/autoload.php';

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/src/Domain'],
    isDevMode: true,
    cache: new ArrayAdapter(),
);

$connection = DriverManager::getConnection([
    'driver'   => 'pdo_mysql',
    'host'     => '127.0.0.1',
    'port'     => 3307,
    'dbname'   => 'toto',
    'user'     => 'root',
    'password' => 'example',
    'charset'  => 'utf8mb4',
]);

$entityManager = new EntityManager($connection, $config);

$migrationConfig = new PhpFile(__DIR__ . '/migrations.php');

return DependencyFactory::fromEntityManager(
    $migrationConfig,
    new ExistingEntityManager($entityManager)
);