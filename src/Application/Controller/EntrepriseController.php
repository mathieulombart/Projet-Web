<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Offre;
use App\Domain\Entreprise;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class EntrepriseController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function liste(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view    = Twig::fromRequest($request);
        $perPage = 2;
        $page    = isset($args['page']) ? (int)$args['page'] : 1;
        $offset  = ($page - 1) * $perPage;

        $total = $this->em->getRepository(Entreprise::class)
            ->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $entreprises = $this->em->getRepository(Entreprise::class)
            ->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $totalPages = (int) ceil($total / $perPage);

        return $view->render($response, 'entreprise.html.twig', [
            'entreprises'  => $entreprises,
            'filtres'      => ['Tous', 'Tech & IT', 'Cybersécurité', 'Conseil', 'Industrie', 'Finance', 'Santé'],
            'pageCourante' => $page,
            'totalPages'   => max(1, $totalPages),
        ]);
    }

    public function ajoute(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view    = Twig::fromRequest($request);
        $nom     = '';
        $secteur = '';
        $success = false;

        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            $nom     = trim($parsedBody['nom'] ?? '');
            $secteur = trim($parsedBody['secteur'] ?? '');

            if ($nom !== '' && $secteur !== '') {
                $entreprise = new Entreprise($nom, $secteur);
                $this->em->persist($entreprise);
                $this->em->flush();
                $success = true;
            }
        }

        return $view->render($response, 'form-entreprise.html.twig', [
            'nom'     => $nom,
            'secteur' => $secteur,
            'success' => $success,
        ]);
    }

    public function modifier(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view       = Twig::fromRequest($request);
        $id         = (int)$args['id'];
        $entreprise = $this->em->find(Entreprise::class, $id);

        if (!$entreprise) {
            return $response->withStatus(404);
        }

        $success = false;

        if ($request->getMethod() === 'POST') {
            $parsedBody = $request->getParsedBody();
            $nom     = trim($parsedBody['nom'] ?? '');
            $secteur = trim($parsedBody['secteur'] ?? '');

            if ($nom !== '' && $secteur !== '') {
                $entreprise->setNom($nom);
                $entreprise->setSecteur($secteur);
                $this->em->flush();
                $success = true;
            }
        }

        return $view->render($response, 'form-entreprise.html.twig', [
            'entreprise' => $entreprise,
            'success'    => $success,
        ]);
    }

    public function supprimer(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id         = (int)$args['id'];
        $entreprise = $this->em->find(Entreprise::class, $id);

        if ($entreprise) {
            $this->em->remove($entreprise);
            $this->em->flush();
        }

        return $response->withHeader('Location', '/entreprises')->withStatus(302);
    }

    public function offres(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $view       = Twig::fromRequest($request);
        $id         = (int)$args['id'];
        $entreprise = $this->em->find(Entreprise::class, $id);

        if (!$entreprise) {
            return $response->withStatus(404);
        }

        $offres = $this->em->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->where('o.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->getResult();

        return $view->render($response, 'entreprise_offres.html.twig', [
            'entreprise' => $entreprise,
            'offres'     => $offres,
        ]);
    }
}