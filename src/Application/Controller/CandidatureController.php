<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Domain\Candidature;
use App\Domain\Offre;
use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
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

        $uploadedFiles = $request->getUploadedFiles();
        /** @var UploadedFileInterface|null $cvFile */
        $cvFile = $uploadedFiles['cv'] ?? null;

        $erreurs = [];
        if ($motivation === '') $erreurs[] = 'La lettre de motivation est requise.';

        if (!$cvFile || $cvFile->getError() !== UPLOAD_ERR_OK) {
            $erreurs[] = 'Le CV est requis.';
        } 
        else {
            $originalName = $cvFile->getClientFilename() ?? '';
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if ($extension !== 'pdf') {
                $erreurs[] = 'Le CV doit être au format PDF.';
            }
            if ($cvFile->getSize() !== null && $cvFile->getSize() > 5 * 1024 * 1024) {
                $erreurs[] = 'Le CV dépasse la taille maximale autorisée (5 Mo).';
            }
        }

        if (!empty($erreurs)) {
            return Twig::fromRequest($request)->render($response, 'postuler.html.twig', [
                'offre'   => $offre,
                'erreurs' => $erreurs,
            ]);
        }

        
        $existe = $this->em->getRepository(Candidature::class)->findOneBy([
            'offre'       => $offre,
            'utilisateur' => $utilisateur,
        ]);

        if (!$existe) {
            $uploadDir = __DIR__ . '/../../../public/uploads/cv';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $safeFilename = sprintf(
                'cv_u%d_o%d_%s.pdf',
                $utilisateur->getId(),
                $offre->getId(),
                bin2hex(random_bytes(8))
            );
            $cvFile->moveTo($uploadDir . DIRECTORY_SEPARATOR . $safeFilename);
            $cvPath = '/uploads/cv/' . $safeFilename;
            $candidature = new Candidature($offre, $utilisateur, $motivation, $cvPath);
            $this->em->persist($candidature);
            $this->em->flush();
        }
        return $response->withHeader('Location', '/offres-postulees')->withStatus(302);
    }

    
    public function retirer(Request $request, Response $response, array $args): Response
    {
        $candidature = $this->em->find(Candidature::class, (int)$args['id']);
        $userId = $_SESSION['user_id'] ?? null;

        if (!$candidature) {
            return $response->withHeader('Location', '/offres-postulees')->withStatus(302);
        }
        if ((int)$candidature->getUtilisateur()->getId() !== (int)$userId) {
            return $response->withStatus(403);
        }

        $this->em->remove($candidature);
        $this->em->flush();
        
        return $response->withHeader('Location', '/offres-postulees')->withStatus(302);
    }

    public function candidaturesEtudiant(Request $request, Response $response, array $args): Response
    {
        $etudiantId = (int) $args['id'];

        $etudiant = $this->em->find(Utilisateur::class, $etudiantId);

        if (!$etudiant || $etudiant->getRole() !== 'etudiant') {
            return $response->withStatus(404);
        }

        $piloteId = $_SESSION['user_id'] ?? null;
        if ($etudiant->getPilote()?->getId() !== $piloteId) {
            return $response->withStatus(403);
        }

        $candidatures = $this->em->getRepository(Candidature::class)->findBy([
            'utilisateur' => $etudiant,
        ]);

        return Twig::fromRequest($request)->render($response, 'offres_postulees.html.twig', [
            'candidatures' => $candidatures,
            'etudiant'     => $etudiant,
            'user_role'    => 'pilote',
        ]);
    }
}
