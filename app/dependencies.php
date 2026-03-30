<?php

use App\Application\Middleware\SessionTwigMiddleware;
use App\Application\Controller\AuthController; 
use App\Application\Controller\UtilisateurController;
use DI\ContainerBuilder;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Slim\Views\Twig;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        Twig::class => function () {
            return Twig::create(__DIR__ . '/../src/Application/templates', [
                'cache' => false,
            ]);
        },

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
        SessionTwigMiddleware::class => function ($c) {
            return new SessionTwigMiddleware($c->get(Twig::class));
        },
        UtilisateurController::class => function ($c) {
            return new UtilisateurController(
                $c->get(EntityManager::class),
                $c->get(Twig::class)
            );
        },
    ]);
};