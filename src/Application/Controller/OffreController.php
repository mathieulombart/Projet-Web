<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Campus;
use App\Domain\Entreprise;
use App\Domain\Offre;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class OffreController
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function detail(Request $request, Response $response, array $args): Response
    {
        $view  = Twig::fromRequest($request);
        $id    = (int)$args['id'];
        $offre = $this->em->find(Offre::class, $id);

        if (!$offre) {
            return $response->withStatus(404);
        }

        return $view->render($response, 'offre_detail.html.twig', [
            'offre' => $offre,
        ]);
    }

    public function ajoute(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);

        
        if ($request->getMethod() === 'GET') {
            return $view->render($response, 'form-offre.html.twig', [
                'offre'       => null,
                'erreurs'     => [],
                'entreprises' => $this->em->getRepository(Entreprise::class)->findBy([], ['nom' => 'ASC']),
                'campus'      => $this->em->getRepository(Campus::class)->findBy([], ['ville' => 'ASC']),
            ]);
        }

        $data         = $request->getParsedBody();
        $titre        = trim($data['titre'] ?? '');
        $description  = trim($data['description'] ?? '');
        $domaine      = trim($data['domaine'] ?? '');
        $localisation = trim($data['localisation'] ?? '');
        $dureeSemaines= trim($data['dureeSemaines'] ?? '') !== '' ? (int)$data['dureeSemaines'] : null;
        $remuneration = ($data['remuneration'] ?? '') !== '' ? (int)$data['remuneration'] : null;

        $offre = new Offre($titre, $description, $domaine, $localisation);
        $offre->setRemuneration($remuneration);
        $offre->setDureeSemaines($dureeSemaines);

        $entreprise = $this->em->find(Entreprise::class, (int)($data['entreprise_id'] ?? 0));
        $offre->setEntreprise($entreprise);

        $campus = $this->em->find(Campus::class, (int)($data['campus_id'] ?? 0));
        $offre->setCampus($campus);
        

        $this->em->persist($offre);
        $this->em->flush();

        return $response
            ->withHeader('Location', '/offres')
            ->withStatus(302);
    }

    public function modifier(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $id = (int) $args['id'];

        $offre = $this->em->find(Offre::class, $id);

        if (!$offre) {
            return $response->withStatus(404);
        }

        if ($request->getMethod() === 'GET') {
            return $view->render($response, 'form-offre.html.twig', [
                'offre'       => $offre,
                'edition'     => true,
                'erreurs'     => [],
                'entreprises' => $this->em->getRepository(Entreprise::class)->findBy([], ['nom' => 'ASC']),
                'campus'      => $this->em->getRepository(Campus::class)->findBy([], ['ville' => 'ASC']),
            ]);
        }

        $data = $request->getParsedBody();

        $titre        = trim($data['titre'] ?? '');
        $description  = trim($data['description'] ?? '');
        $domaine      = trim($data['domaine'] ?? '');
        $localisation = trim($data['localisation'] ?? '');
        $dureeSemaines = ($data['dureeSemaines'] ?? '') !== '' ? (int)$data['dureeSemaines'] : null;
        $remuneration = ($data['remuneration'] ?? '') !== '' ? (int)$data['remuneration'] : null;

        $offre->setTitre($titre);
        $offre->setDescription($description);
        $offre->setDomaine($domaine);
        $offre->setLocalisation($localisation);
        $offre->setDureeSemaines($dureeSemaines);
        $offre->setRemuneration($remuneration);

        $entreprise = $this->em->find(Entreprise::class, (int)($data['entreprise_id'] ?? 0));
        $offre->setEntreprise($entreprise);

        $campus = $this->em->find(Campus::class, (int)($data['campus_id'] ?? 0));
        $offre->setCampus($campus);

        $this->em->flush();

        return $response
            ->withHeader('Location', '/offres')
            ->withStatus(302);
    }

    public function supprimer(Request $request, Response $response, array $args): Response
    {
        $id    = (int) $args['id'];
        $offre = $this->em->find(Offre::class, $id);

        if ($offre) {
            $this->em->remove($offre);
            $this->em->flush();
        }

        return $response
            ->withHeader('Location', '/offres')
            ->withStatus(302);
    }
    public function liste(Request $request, Response $response, array $args): Response
    {
        $view = Twig::fromRequest($request);
        $perPage = 10;
        $page =isset($args['page']) ? (int)$args['page'] : 1;

        $params = $request ->getQueryParams();
        $search = trim($params['q'] ?? '');

        $qb = $this->em->getRepository(Offre::class)->createQueryBuilder('o');

        if ($search !== '') {
            $qb->andWhere('o.titre LIKE :term OR o.description LIKE :term OR o.localisation LIKE :term')
               ->setParameter('term', '%' . $search . '%');
        }

        
        if (($_SESSION['user_role'] ?? '') === 'etudiant' && !empty($_SESSION['user_id'])) {
            $utilisateur = $this->em->find(\App\Domain\Utilisateur::class, $_SESSION['user_id']);
            if ($utilisateur && $utilisateur->getCampus()) {
                $campusId = $utilisateur->getCampus()->getId();
                $qb->andWhere('IDENTITY(o.campus) = :campusId OR o.campus IS NULL')
                   ->setParameter('campusId', $campusId);
            }
        }

        
        $totalQb = clone $qb;
        $total   = (int) $totalQb->select('COUNT(o.id)')->getQuery()->getSingleScalarResult();

        $offset = ($page - 1) * $perPage;
        $offres = $qb
            ->select('o')
            ->orderBy('o.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $totalPages = (int) ceil($total / $perPage);

        return $view->render($response, 'offre.html.twig', [
            'offres'        => $offres,
            'pageCourante'  => $page,
            'totalPages'    => max(1, $totalPages),
            'search'        => $search,
        ]);
    }
}
