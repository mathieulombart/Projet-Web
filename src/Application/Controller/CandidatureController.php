<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Candidature;
use App\Domain\Offre;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CandidatureController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    // GET /postuler/{id} — affiche le formulaire
    public function formulaire(Request $request, Response $response, array $args): Response
    {
        $offre = $this->em->find(Offre::class, (int)$args['id']);

        if (!$offre) {
            return $response->withStatus(404);
        }

        return Twig::fromRequest($request)->render($response, 'postuler.html.twig', [
            'offre'   => $offre,
            'erreurs' => [],
        ]);
    }

    // POST /postuler/{id} — valide et enregistre en BDD
    public function postuler(Request $request, Response $response, array $args): Response
    {
        $offre = $this->em->find(Offre::class, (int)$args['id']);

        if (!$offre) {
            return $response->withStatus(404);
        }

        $data       = $request->getParsedBody();
        $motivation = trim($data['motivation'] ?? '');

        $erreurs = [];
        if ($motivation === '') $erreurs[] = 'La lettre de motivation est requise.';

        if (!empty($erreurs)) {
            return Twig::fromRequest($request)->render($response, 'postuler.html.twig', [
                'offre'   => $offre,
                'erreurs' => $erreurs,
            ]);
        }

        $this->em->persist(new Candidature($offre, $motivation));
        $this->em->flush();

        return $response->withHeader('Location', '/offres-postulees')->withStatus(302);
    }

    // POST /candidature/retirer/{id} — supprime la candidature
    public function retirer(Request $request, Response $response, array $args): Response
    {
        $candidature = $this->em->find(Candidature::class, (int)$args['id']);

        if ($candidature) {
            $this->em->remove($candidature);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/offres-postulees')->withStatus(302);
    }
}
