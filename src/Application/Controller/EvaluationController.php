<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Entreprise;
use App\Domain\Evaluation;
use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class EvaluationController
{
    private EntityManager $entityManager;
    private Twig $twig;

    public function __construct(EntityManager $entityManager, Twig $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function formulaire(Request $request, Response $response, array $args): Response
    {
        $entreprise = $this->entityManager->find(Entreprise::class, (int) $args['id']);
        $stats = $entreprise ? $this->getStatsEntreprise($entreprise) : ['moyenne' => null, 'total' => 0];
        return $this->twig->render($response, 'evaluation.html.twig', [
            'entreprise'  => $entreprise,
            'errors'      => [],
            'success'     => false,
            'old'         => [],
            'evaluations' => [],
            'moyenne'     => $stats['moyenne'],
            'totalAvis'   => $stats['total'],
        ]);
    }

    public function evaluer(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();

        $note = (int) ($data['note'] ?? 0);
        $commentaire = trim($data['commentaire'] ?? '');
        $errors = [];

        $entreprise = $this->entityManager->find(Entreprise::class, (int) $args['id']);
        $utilisateur = $this->entityManager->find(Utilisateur::class, 1);
        $stats = $entreprise ? $this->getStatsEntreprise($entreprise) : ['moyenne' => null, 'total' => 0];

        if ($note < 1 || $note > 5) {
            $errors[] = 'La note doit être comprise entre 1 et 5.';
        }

        if ($commentaire === '') {
            $errors[] = 'Le commentaire est obligatoire.';
        }

        if (!$entreprise) {
            $errors[] = 'Entreprise introuvable.';
        }

        if (!$utilisateur) {
            $errors[] = 'Utilisateur introuvable.';
        }

        if (!empty($errors)) {
            return $this->twig->render($response, 'evaluation.html.twig', [
                'entreprise'  => $entreprise,
                'errors'      => $errors,
                'success'     => false,
                'old'         => $data,
                'evaluations' => [],
                'moyenne'     => $stats['moyenne'],
                'totalAvis'   => $stats['total'],

            ]);
        }

        $evaluation = new Evaluation($note, $commentaire, $entreprise, $utilisateur);
        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        $routeParser = \Slim\Routing\RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor('entreprise-evaluer', ['id' => $args['id']]);

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
    private function getStatsEntreprise(Entreprise $entreprise): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $result = $qb
            ->select('AVG(e.note) AS moyenne', 'COUNT(e.id) AS total')
            ->from(Evaluation::class, 'e')
            ->where('e.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->getSingleResult();

        return [
            'moyenne' => $result['moyenne'] !== null ? (float) $result['moyenne'] : null,
            'total' => (int) $result['total'],
        ];
    }
}