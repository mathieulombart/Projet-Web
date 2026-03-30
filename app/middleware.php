<?php

declare(strict_types=1);

use App\Application\Middleware\SessionTwigMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;


return function (App $app) {
    $app->add($app->getContainer()->get(SessionTwigMiddleware::class));
    $twig = $app->getContainer()->get(Twig::class);
    $app->add(TwigMiddleware::create($app, $twig));
    $app->add(new SessionMiddleware());
};
