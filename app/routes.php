<?php

declare(strict_types=1);

use App\Application\Controller\AuthController;
use App\Application\Controller\EntrepriseController;
use App\Application\Controller\HomeController;
use App\Application\Controller\OffreController;
use App\Application\Controller\ProfilController;
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

    // Page d'accueil
    $app->get('/', [HomeController::class, 'home']);

    // Entreprises (CRUD)
    $app->get('/entreprises[/{page:\d+}]', [EntrepriseController::class, 'liste'])
        ->setName('liste-entreprises');
    $app->get('/ajout-entreprise', [EntrepriseController::class, 'ajoute'])
        ->setName('ajout-entreprise')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    $app->post('/ajout-entreprise', [EntrepriseController::class, 'ajoute'])
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    $app->get('/modifier-entreprise/{id:\d+}', [EntrepriseController::class, 'modifier'])
        ->setName('modifier-entreprise')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    $app->post('/modifier-entreprise/{id:\d+}', [EntrepriseController::class, 'modifier'])
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    $app->post('/supprimer-entreprise/{id:\d+}', [EntrepriseController::class, 'supprimer'])
        ->setName('supprimer-entreprise')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    // Offres
    $app->get('/offres[/{page:\d+}]', [OffreController::class, 'liste'])->setName('liste-offres');
    $app->get('/offre/{id:\d+}', [OffreController::class, 'detail'])->setName('detail-offre');

    // Offres entreprise
    $app->get('/entreprise/{id:\d+}/offres', [EntrepriseController::class, 'offres'])->setName('entreprise-offres');

    // Pages statiques
    $app->get('/inscription', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'inscription.html.twig', []);
        })
        ->setName('inscription')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    $app->post('/inscription', [AuthController::class, 'inscription'])
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    

    /*$app->get('/profil', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'profil.html.twig', []);
    })->setName('profil');*/

    $app->get('/profil', [ProfilController::class, 'index'])->setName('profil')
        ->add(new AuthMiddleware());

    $app->get('/postuler', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'postuler.html.twig', []);
        })
        ->setName('postuler')
        ->add(new AuthMiddleware());

    // Pages statiques supplémentaires
    $app->get('/contact', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'contact.html.twig', []);
    })->setName('app_contact');

    $app->get('/mention', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'mention.html.twig', []);
    })->setName('app_mention');

    $app->get('/confidentialité', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'confidentialité.html.twig', []);
    })->setName('app_confidentialité');

    
    $app->get('/wishlist', [ProfilController::class, 'wishlist'])
        ->setName('wishlist')
        ->add(new AuthMiddleware());

        // routes.php

    $app->post('/wishlist', function ($request, $response) {
        // ... logique d'ajout ...
        return $response->withHeader('Location', '/profil')->withStatus(302);
        })
        ->setName('wishlist')
        ->add(new AuthMiddleware());

    $app->get('/offres-postulees', [ProfilController::class, 'offresPostulees'])
        ->setName('offres-postulees')
        ->add(new AuthMiddleware());
        
    // >>> Formulaire de création d'offre (GET) <<<
    $app->get('/ajout-offre', [OffreController::class, 'ajoute'])
        ->setName('ajout-offre')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());

    // >>> Traitement du formulaire (POST) <<<
    $app->post('/ajout-offre', [OffreController::class, 'ajoute'])
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());

    // Supprimer une offre
    $app->post('/offre/supprimer/{id:\d+}', [OffreController::class, 'supprimer'])
        ->setName('offre-supprimer')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());

    $app->post('/wishlist/ajouter', function ($request, $response) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $data = $request->getParsedBody();
        $offreId = $data['offre_id'] ?? null;

        if ($offreId) {
            if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

            // On crée un tableau avec les infos pour l'affichage
            // Note : Dans un vrai projet, on chercherait ces infos en BDD via l'ID
            $_SESSION['wishlist'][$offreId] = [
                'id'           => $offreId,
                'intitule'     => $data['titre'] ?? 'Poste sans titre',
                'entreprise'   => $data['entreprise'] ?? 'N/A',
                'localisation' => $data['localisation'] ?? 'Non précisée',
                'duree'        => 'Stage' // Valeur par défaut
            ];
        }
        return $response->withHeader('Location', '/wishlist')->withStatus(302);
    })
        ->setName('wishlist-ajouter')
        ->add(new AuthMiddleware());


    $app->post('/wishlist/supprimer/{id:\d+}', function ($request, $response, $args) {
        if (session_status() === PHP_SESSION_NONE) session_start();
    
    $idOffre = $args['id'];

        if (isset($_SESSION['wishlist'][$idOffre])) {
            unset($_SESSION['wishlist'][$idOffre]);
        }
        return $response->withHeader('Location', '/wishlist')->withStatus(302);
    })
        ->setName('wishlist-supprimer')
        ->add(new AuthMiddleware());

    $app->get('/connexion', [AuthController::class, 'connexion'])->setName('connexion');
    $app->post('/connexion', [AuthController::class, 'connexion']);
};
