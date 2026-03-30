<?php
declare(strict_types=1);
namespace App\Application\Controller;

use App\Domain\Wishlist;
use App\Domain\Offre;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class WishlistController
{
    public function __construct(private EntityManager $em, private Twig $twig) {}

    public function index(Request $request, Response $response): Response
    {
        // On récupère Twig via la requête pour éviter l'erreur de Runtime Extension
        $view = Twig::fromRequest($request);
        $wishlistItems = $this->em->getRepository(Wishlist::class)->findAll();

        return $view->render($response, 'wishlist.html.twig', [
            'wishlist' => $wishlistItems,
        ]);
    }

    public function ajouter(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $offreId = (int)($data['offre_id'] ?? 0);

        if ($offreId > 0) {
            $offre = $this->em->find(Offre::class, $offreId);
            if ($offre) {
                $existe = $this->em->getRepository(Wishlist::class)->findOneBy(['offre' => $offre]);
                if (!$existe) {
                    $this->em->persist(new Wishlist($offre));
                    $this->em->flush();
                }
            }
        }
        return $response->withHeader('Location', '/offres')->withStatus(302);
    }

    public function supprimer(Request $request, Response $response, array $args): Response
    {
        $item = $this->em->find(Wishlist::class, (int)$args['id']);
        if ($item) {
            $this->em->remove($item);
            $this->em->flush();
        }
        return $response->withHeader('Location', '/wishlist')->withStatus(302);
    }
}