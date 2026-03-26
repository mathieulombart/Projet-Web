<?php

use App\Application\Controller\AuthController; 
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Slim\Views\Twig;
use Symfony\Component\Cache\Adapter\ArrayAdapter;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        EntityManager::class => function () {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [__DIR__ . '/../src/Domain'],
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

            return new EntityManager($connection, $config);
        },
                AuthController::class => function ($c) {
            return new AuthController(
                $c->get(EntityManager::class),
                $c->get(Twig::class)
            );
        },
    ]);
};