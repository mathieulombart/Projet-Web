<?php

declare(strict_types=1);

use App\Application\Controller\AuthController;
use App\Application\Controller\CampusController;
use App\Application\Controller\CandidatureController;
use App\Application\Controller\EntrepriseController;
use App\Application\Controller\HomeController;
use App\Application\Controller\OffreController;
use App\Application\Controller\ProfilController;
use App\Application\Controller\WishlistController;
use App\Application\Controller\UtilisateurController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\Twig;
use App\Application\Middleware\RoleMiddleware;
use App\Application\Middleware\AuthMiddleware;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    // --- HOME ---
    $app->get('/', [HomeController::class, 'home']);

    // --- CAMPUS ---
    $app->get('/campus', [CampusController::class, 'liste'])
        ->setName('campus')
        ->add(new RoleMiddleware(['admin']))
        ->add(new AuthMiddleware());

    $app->post('/campus/ajouter', [CampusController::class, 'ajouter'])
        ->setName('campus-ajouter')
        ->add(new RoleMiddleware(['admin']))
        ->add(new AuthMiddleware());

    $app->get('/campus/modifier/{id:\d+}', [CampusController::class, 'modifier'])
        ->setName('campus-modifier')
        ->add(new RoleMiddleware(['admin']))
        ->add(new AuthMiddleware());

    $app->post('/campus/modifier/{id:\d+}', [CampusController::class, 'modifier'])
        ->add(new RoleMiddleware(['admin']))
        ->add(new AuthMiddleware());

    $app->post('/campus/supprimer/{id:\d+}', [CampusController::class, 'supprimer'])
        ->setName('campus-supprimer')
        ->add(new RoleMiddleware(['admin']))
        ->add(new AuthMiddleware());

    // --- ENTREPRISES ---
    $app->get('/entreprises[/{page:\d+}]', [EntrepriseController::class, 'liste'])
        ->setName('liste-entreprises');

    $app->get('/ajout-entreprise', [EntrepriseController::class, 'ajoute'])
        ->setName('ajout-entreprise')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/ajout-entreprise', [EntrepriseController::class, 'ajoute'])
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->get('/modifier-entreprise/{id:\d+}', [EntrepriseController::class, 'modifier'])
        ->setName('modifier-entreprise')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/modifier-entreprise/{id:\d+}', [EntrepriseController::class, 'modifier'])
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/supprimer-entreprise/{id:\d+}', [EntrepriseController::class, 'supprimer'])
        ->setName('supprimer-entreprise')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    // --- OFFRES ---
    $app->get('/offres[/{page:\d+}]', [OffreController::class, 'liste'])
        ->setName('liste-offres');

    $app->get('/offre/{id:\d+}', [OffreController::class, 'detail'])
        ->setName('detail-offre');

    $app->get('/ajout-offre', [OffreController::class, 'ajoute'])
        ->setName('ajout-offre')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/ajout-offre', [OffreController::class, 'ajoute'])
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->get('/modifier-offre/{id:\d+}', [OffreController::class, 'modifier'])
        ->setName('modifier-offre')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/modifier-offre/{id:\d+}', [OffreController::class, 'modifier'])
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/offre/supprimer/{id:\d+}', [OffreController::class, 'supprimer'])
        ->setName('offre-supprimer')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    // Offres par entreprise
    $app->get('/entreprise/{id:\d+}/offres', [EntrepriseController::class, 'offres'])
        ->setName('entreprise-offres');

    // --- WISHLIST (Controller dédié) ---
    $app->get('/wishlist', [WishlistController::class, 'index'])
        ->setName('wishlist')
        ->add(new AuthMiddleware());

    $app->post('/wishlist/ajouter', [WishlistController::class, 'ajouter'])
        ->setName('wishlist-ajouter')
        ->add(new AuthMiddleware());

    $app->post('/wishlist/supprimer/{id:\d+}', [WishlistController::class, 'supprimer'])
        ->setName('wishlist-supprimer')
        ->add(new AuthMiddleware());

    // --- AUTH & PROFIL ---
    $app->get('/inscription', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'inscription.html.twig', []);
    })
        ->setName('inscription')
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/inscription', [AuthController::class, 'inscription'])
        ->add(new RoleMiddleware(['admin'], ['pilote']))
        ->add(new AuthMiddleware());

    $app->get('/profil', [ProfilController::class, 'index'])
        ->setName('profil')
        ->add(new AuthMiddleware());

    $app->get('/connexion', [AuthController::class, 'connexion'])
        ->setName('connexion');

    $app->post('/connexion', [AuthController::class, 'connexion']);

    $app->get('/deconnexion', [AuthController::class, 'deconnexion'])
        ->setName('deconnexion');

    // --- CANDIDATURES ---
    $app->get('/postuler/{id:\d+}', [CandidatureController::class, 'formulaire'])
        ->setName('postuler');

    $app->post('/postuler/{id:\d+}', [CandidatureController::class, 'postuler']);

    $app->post('/candidature/retirer/{id:\d+}', [CandidatureController::class, 'retirer'])
        ->setName('candidature-retirer');

    $app->get('/offres-postulees', [ProfilController::class, 'offresPostulees'])
        ->setName('offres-postulees')
        ->add(new AuthMiddleware());

    // --- PAGES STATIQUES ---
    $app->get('/contact', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'contact.html.twig', []);
    })->setName('app_contact');

    $app->get('/mention', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'mention.html.twig', []);
    })->setName('app_mention');

    $app->get('/confidentialité', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'confidentialité.html.twig', []);
    })->setName('app_confidentialité');

    $app->get('/permission', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'permission.html.twig', []);
    })->setName('permission');
    // Ajoute ->setName('utilisateurs') à chaque route
    $app->get('/utilisateurs', [UtilisateurController::class, 'liste'])
        ->add(new AuthMiddleware())
        ->setName('utilisateurs');

    $app->get('/utilisateurs/{page:[0-9]+}', [UtilisateurController::class, 'liste'])
        ->add(new AuthMiddleware())
        ->setName('utilisateurs_page');
};