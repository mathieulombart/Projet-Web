<?php

declare(strict_types=1);

use App\Application\Controller\AuthController;
use App\Application\Controller\CandidatureController;
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


    $app->get('/', [HomeController::class, 'home']);

    
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
   
    $app->get('/offres[/{page:\d+}]', [OffreController::class, 'liste'])->setName('liste-offres');
    $app->get('/offre/{id:\d+}', [OffreController::class, 'detail'])->setName('detail-offre');

   
    $app->get('/entreprise/{id:\d+}/offres', [EntrepriseController::class, 'offres'])->setName('entreprise-offres');


    $app->get('/inscription', function (Request $request, Response $response) {
        return Twig::fromRequest($request)->render($response, 'inscription.html.twig', []);
        })
        ->setName('inscription')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());
    $app->post('/inscription', [AuthController::class, 'inscription'])
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());

    

    $app->get('/profil', [ProfilController::class, 'index'])->setName('profil')
        ->add(new AuthMiddleware());

    $app->get('/postuler/{id:\d+}', [CandidatureController::class, 'formulaire'])->setName('postuler');
    $app->post('/postuler/{id:\d+}', [CandidatureController::class, 'postuler']);
    $app->post('/candidature/retirer/{id:\d+}', [CandidatureController::class, 'retirer'])->setName('candidature-retirer');

  
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

       

    $app->post('/wishlist', function ($request, $response) {
       
        return $response->withHeader('Location', '/profil')->withStatus(302);
        })
        ->setName('wishlist')
        ->add(new AuthMiddleware());

    $app->get('/offres-postulees', [ProfilController::class, 'offresPostulees'])
        ->setName('offres-postulees')
        ->add(new AuthMiddleware());
        
  
    $app->get('/ajout-offre', [OffreController::class, 'ajoute'])
        ->setName('ajout-offre')
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());

    
    $app->post('/ajout-offre', [OffreController::class, 'ajoute'])
        ->add(new RoleMiddleware(['admin'],['pilote']))
        ->add(new AuthMiddleware());

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
    $app->get('/deconnexion', [AuthController::class, 'deconnexion'])->setName('deconnexion');
};
