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

        return $this->twig->render($response, 'evaluation.html.twig', [
            'entreprise'  => $entreprise,
            'errors'      => [],
            'success'     => false,
            'old'         => [],
            'evaluations' => [],
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
            ]);
        }

        $evaluation = new Evaluation($note, $commentaire, $entreprise, $utilisateur);
        $this->entityManager->persist($evaluation);
        $this->entityManager->flush();

        return $response
            ->withHeader('Location', '/entreprise/' . $args['id'])
            ->withStatus(302);
    }
}