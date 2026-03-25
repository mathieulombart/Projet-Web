<?php

declare(strict_types=1);

namespace App\Application\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfilController
{
    public function wishlist(Request $request, Response $response, array $args): Response
{
    $view = Twig::fromRequest($request);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // On lit directement les offres complètes stockées en session
    $offresWishlist = $_SESSION['wishlist'] ?? [];

    return $view->render($response, 'wishlist.html.twig', [
        'offres_wishlist' => $offresWishlist,
    ]);
}

    public function offresPostulees(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);

        $candidatures = [];

        return $view->render($response, 'offres_postulees.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    // Cette méthode gère l'affichage du profil (avec le badge)
    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        // On récupère les IDs stockés en session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $wishlistIds = $_SESSION['wishlist'] ?? [];
        $nbWishlist = count($wishlistIds);

        // Données utilisateur "en dur" (sans BDD)
        $utilisateur = [
            'nom' => 'Jean Dupont',
            'statut' => 'Étudiant',
            'ecole' => 'CESI',
            'pilote' => 'M. Martin',
            'email' => 'jean.dupont@exemple.com',
            'ville' => 'Paris'
        ];

        return $view->render($response, 'profil.html.twig', [
            'nbWishlist' => $nbWishlist,
            'utilisateur' => $utilisateur,
        ]);
    }
}

