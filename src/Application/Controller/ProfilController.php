<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Candidature;
use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfilController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public function wishlist(Request $request, Response $response, array $args): Response
{
    $view = Twig::fromRequest($request);

    $offresWishlist = $_SESSION['wishlist'] ?? [];

    return $view->render($response, 'wishlist.html.twig', [
        'offres_wishlist' => $offresWishlist,
    ]);
}

    public function offresPostulees(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);

        $utilisateur = $this->em->find(Utilisateur::class, $_SESSION['user_id'] ?? null);

        if (!$utilisateur) {
            return $response->withHeader('Location', '/connexion')->withStatus(302);
        }

        $candidatures = $this->em->getRepository(Candidature::class)
            ->findBy(['utilisateur' => $utilisateur], ['dateCandidature' => 'DESC']);

        return $view->render($response, 'offres_postulees.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

   
    public function index(Request $request, Response $response): Response
    {
        $view = Twig::fromRequest($request);

        $wishlistIds = $_SESSION['wishlist'] ?? [];
        $nbWishlist = count($wishlistIds);

    // On récupère l'utilisateur depuis la BDD grâce à l'id en session
        $utilisateur = $this->em->getRepository(\App\Domain\Utilisateur::class)
            ->find($_SESSION['user_id']);
        
        if (!$utilisateur) {
            return $response->withHeader('Location', '/connexion')->withStatus(302);
        }

        return $view->render($response, 'profil.html.twig', [
            'nbWishlist'  => $nbWishlist,
            'utilisateur' => $utilisateur,
        ]);
    }
}

