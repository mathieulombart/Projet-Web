<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Campus;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class CampusController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function liste(Request $request, Response $response): Response
    {
        $view    = Twig::fromRequest($request);
        $campus  = $this->em->getRepository(Campus::class)->findBy([], ['ville' => 'ASC']);

        return $view->render($response, 'campus.html.twig', [
            'campus'  => $campus,
            'success' => $request->getQueryParams()['success'] ?? null,
        ]);
    }

    public function ajouter(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data  = $request->getParsedBody();
            $ville = trim($data['ville'] ?? '');

            if ($ville !== '') {
                $this->em->persist(new Campus($ville));
                $this->em->flush();
            }

            return $response->withHeader('Location', '/campus?success=ajoute')->withStatus(302);
        }

        return Twig::fromRequest($request)->render($response, 'campus.html.twig', [
            'campus' => $this->em->getRepository(Campus::class)->findBy([], ['ville' => 'ASC']),
        ]);
    }

    public function modifier(Request $request, Response $response, array $args): Response
    {
        $campus = $this->em->find(Campus::class, (int)$args['id']);

        if (!$campus) {
            return $response->withStatus(404);
        }

        if ($request->getMethod() === 'POST') {
            $data  = $request->getParsedBody();
            $ville = trim($data['ville'] ?? '');

            if ($ville !== '') {
                $campus->setVille($ville);
                $this->em->flush();
            }

            return $response->withHeader('Location', '/campus?success=modifie')->withStatus(302);
        }

        return Twig::fromRequest($request)->render($response, 'campus-form.html.twig', [
            'campus' => $campus,
        ]);
    }

    public function supprimer(Request $request, Response $response, array $args): Response
    {
        $campus = $this->em->find(Campus::class, (int)$args['id']);

        if ($campus) {
            $this->em->remove($campus);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/campus?success=supprime')->withStatus(302);
    }
}
