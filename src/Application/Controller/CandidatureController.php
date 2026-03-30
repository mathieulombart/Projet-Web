<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Candidature;
use App\Domain\Offre;
use App\Domain\Utilisateur;
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

    
    public function postuler(Request $request, Response $response, array $args): Response
    {
        $offre = $this->em->find(Offre::class, (int)$args['id']);

        if (!$offre) {
            return $response->withStatus(404);
        }

        $utilisateur = $this->em->find(Utilisateur::class, $_SESSION['user_id'] ?? null);

        if (!$utilisateur) {
            return $response->withHeader('Location', '/connexion')->withStatus(302);
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

        // Éviter les doublons de candidature
        $existe = $this->em->getRepository(Candidature::class)->findOneBy([
            'offre'       => $offre,
            'utilisateur' => $utilisateur,
        ]);

        if (!$existe) {
            $this->em->persist(new Candidature($offre, $utilisateur, $motivation));
            $this->em->flush();
        }

        return $response->withHeader('Location', '/offres-postulees')->withStatus(302);
    }

    
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
