<?php

namespace App\Application\Controller;

use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class UtilisateurController
{
    public function __construct(
        private EntityManager $entityManager,
        private Twig $twig
    ) {}

    public function liste(Request $request, Response $response, array $args): Response
    {
        $pageCourante = (int)($args['page'] ?? 1);
        $parPage = 10;
        $search = $request->getQueryParams()['q'] ?? '';
        $userRole = $_SESSION['user_role'] ?? null;
        $userId   = $_SESSION['user_id']   ?? null;

        $repo = $this->entityManager->getRepository(Utilisateur::class);
        $qb   = $repo->createQueryBuilder('u');

        // Filtre selon le rôle
        if ($userRole === 'pilote') {
            // Un pilote ne voit que ses étudiants
            $qb->where('u.role = :role')
               ->andWhere('u.pilote = :piloteId')
               ->setParameter('role', Utilisateur::ROLE_ETUDIANT)
               ->setParameter('piloteId', $userId);
        } elseif ($userRole === 'admin') {
            // L'admin voit tout le monde
        }

        // Recherche textuelle
        if (!empty($search)) {
            $qb->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Pagination
        $total = (clone $qb)->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $totalPages = max(1, ceil($total / $parPage));

        $utilisateurs = $qb
            ->setFirstResult(($pageCourante - 1) * $parPage)
            ->setMaxResults($parPage)
            ->getQuery()
            ->getResult();

        return $this->twig->render($response, 'utilisateurs.html.twig', [
            'utilisateurs' => $utilisateurs,
            'pageCourante' => $pageCourante,
            'totalPages'   => $totalPages,
            'search'       => $search,
        ]);
    }
}