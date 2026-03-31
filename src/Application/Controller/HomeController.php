<?php

namespace App\Application\Controller;

use App\Domain\Candidature;
use App\Domain\Offre;
use App\Domain\Wishlist;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $conn = $this->em->getConnection();

        // 1. Nombre total d'offres
        $totalOffres = (int) $conn->fetchOne('SELECT COUNT(*) FROM offres');

        // 2. Moyenne de candidatures par offre
        $totalCandidatures = (int) $conn->fetchOne('SELECT COUNT(*) FROM candidatures');
        $moyenneCandidatures = $totalOffres > 0 ? round($totalCandidatures / $totalOffres, 1) : 0;

        // 3. Répartition par durée
        $repartitionDuree = [
            'courte'  => (int) $conn->fetchOne('SELECT COUNT(*) FROM offres WHERE duree_semaines IS NOT NULL AND duree_semaines < 4'),
            'moyenne' => (int) $conn->fetchOne('SELECT COUNT(*) FROM offres WHERE duree_semaines >= 4 AND duree_semaines <= 8'),
            'longue'  => (int) $conn->fetchOne('SELECT COUNT(*) FROM offres WHERE duree_semaines > 8'),
            'non_renseignee' => (int) $conn->fetchOne('SELECT COUNT(*) FROM offres WHERE duree_semaines IS NULL'),
        ];

        // 4. Top 5 offres les plus en wishlist
        $topWishlist = $conn->fetchAllAssociative(
            'SELECT o.id, o.titre, COUNT(w.id) as nb
             FROM wishlists w
             JOIN offres o ON o.id = w.offre_id
             GROUP BY o.id, o.titre
             ORDER BY nb DESC
             LIMIT 5'
        );

        return $view->render($response, 'accueil.html.twig', [
            'totalOffres'          => $totalOffres,
            'moyenneCandidatures'  => $moyenneCandidatures,
            'repartitionDuree'     => $repartitionDuree,
            'topWishlist'          => $topWishlist,
        ]);
    }
}