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

        // Pour l'instant, liste vide ou données mock
        $offresWishlist = [];

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
}
