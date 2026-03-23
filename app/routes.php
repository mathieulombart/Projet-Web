<?php

declare(strict_types=1);

use App\Application\Controller\EntrepriseController;
use App\Application\Controller\HomeController;
use App\Application\Controller\OffreController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\Twig;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    // Page d'accueil
    $app->get('/', [HomeController::class, 'home']);

    // Entreprises (CRUD)
    $app->get('/entreprises[/{page:\d+}]', [EntrepriseController::class, 'liste'])->setName('liste-entreprises');
    $app->get('/ajout-entreprise', [EntrepriseController::class, 'ajoute'])->setName('ajout-entreprise');
    $app->post('/ajout-entreprise', [EntrepriseController::class, 'ajoute']);
    $app->get('/modifier-entreprise/{id:\d+}', [EntrepriseController::class, 'modifier'])->setName('modifier-entreprise');
    $app->post('/modifier-entreprise/{id:\d+}', [EntrepriseController::class, 'modifier']);
    $app->post('/supprimer-entreprise/{id:\d+}', [EntrepriseController::class, 'supprimer'])->setName('supprimer-entreprise');

    // Offres
    $app->get('/offres[/{page:\d+}]', [OffreController::class, 'liste'])->setName('liste-offres');
    $app->get('/offre/{id:\d+}', [OffreController::class, 'detail'])->setName('detail-offre');

    // Pages statiques
    $app->get('/connexion', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'connexion.html.twig', []);
    });
    $app->get('/inscription', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'inscription.html.twig', []);
    });
    $app->get('/profil', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'profil.html.twig', []);
    });
    $app->get('/postuler', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'postuler.html.twig', []);
    });
};
