<?php

namespace App\Application\Controller;

use App\Domain\Campus;
use App\Domain\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Twig $twig
    ) {}

    public function inscription(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $erreurs = [];

            if (empty($data['email']))    $erreurs[] = 'L\'email est obligatoire.';
            if (empty($data['identifiant']))    $erreurs[] = 'L\'identifiant est obligatoire.';
            if (empty($data['nom']))      $erreurs[] = 'Le nom est obligatoire.';
            if (empty($data['prenom']))   $erreurs[] = 'Le prénom est obligatoire.';
            if (empty($data['password'])) $erreurs[] = 'Le mot de passe est obligatoire.';
            if ($data['password'] !== $data['password-confirm']) {
                $erreurs[] = 'Les mots de passe ne correspondent pas.';
            }

            if (!empty($erreurs)) {
                return $this->twig->render($response, 'inscription.html.twig', [
                    'erreurs' => $erreurs
                ]);
            }

            $hash = password_hash($data['password'], PASSWORD_BCRYPT);

            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($data['email']);
            $utilisateur->setIdentifiant($data['identifiant']);
            $utilisateur->setNom($data['nom']);
            $utilisateur->setPrenom($data['prenom']);
            $utilisateur->setMotDePasse($hash);
            $utilisateur->setRole($data['role'] ?? Utilisateur::ROLE_ETUDIANT);
            $utilisateur->setPromotion($data['promo'] ?? '');

            $campus = $this->entityManager->find(Campus::class, (int)($data['campus_id'] ?? 0));
            $utilisateur->setCampus($campus);

            if ($data['role'] === 'etudiant') {
                $pilote = $this->entityManager->getRepository(Utilisateur::class)
                    ->findOneBy([
                        'role'      => Utilisateur::ROLE_PILOTE,
                        'promotion' => $data['promo'],
                        'campus'    => $campus,
                    ]);
                if ($pilote) {
                    $utilisateur->setPilote($pilote); // ✅ passe l'objet directement
                }
            }

            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            return $response->withHeader('Location', '/connexion')->withStatus(302);
        }

        $campus = $this->entityManager->getRepository(Campus::class)->findBy([], ['ville' => 'ASC']);
        return $this->twig->render($response, 'inscription.html.twig', ['campus' => $campus]);
    }

    public function connexion(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $utilisateur = $this->entityManager
                ->getRepository(Utilisateur::class)
                ->findOneBy(['identifiant' => $data['identifiant']]);

            if (!$utilisateur) {
                return $this->twig->render($response, 'connexion.html.twig', [
                    'erreur' => 'Identifiant ou mot de passe incorrect.'
                ]);
            }
            if (strlen($utilisateur->getMotDePasse()) >= 20) {
                if (!password_verify($data['password'], $utilisateur->getMotDePasse())) {
                    return $this->twig->render($response, 'connexion.html.twig', [
                        'erreur' => 'Identifiant ou mot de passe incorrect.'
                    ]);
                }
            } else if ($data['password'] != $utilisateur->getMotDePasse()) {
                return $this->twig->render($response, 'connexion.html.twig', [
                    'erreur' => 'Identifiant ou mot de passe incorrect.'
                ]);
            }

            $_SESSION['user_id']   = $utilisateur->getId();
            $_SESSION['user_role'] = $utilisateur->getRole();

            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $this->twig->render($response, 'connexion.html.twig');
    }

    public function deconnexion(Request $request, Response $response): Response
    {
        session_destroy();
        return $response->withHeader('Location', '/connexion')->withStatus(302);
    }

    public function supprimer(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $utilisateur = $this->entityManager
                ->getRepository(Utilisateur::class)
                ->findOneBy([
                    'identifiant' => $data['identifiant'],
                    'email'       => $data['email'],
                ]);

            if (!$utilisateur) {
                return $this->twig->render($response, 'supprimer.html.twig', [
                    'erreur' => 'Aucun utilisateur trouvé avec ces informations.'
                ]);
            }

            if (
                strtolower($utilisateur->getNom())    !== strtolower($data['nom']) ||
                strtolower($utilisateur->getPrenom()) !== strtolower($data['prenom'])
            ) {
                return $this->twig->render($response, 'supprimer.html.twig', [
                    'erreur' => 'Les informations saisies ne correspondent pas.'
                ]);
            }

            $this->entityManager->remove($utilisateur);
            $this->entityManager->flush();

            return $response->withHeader('Location', '/utilisateurs')->withStatus(302);
        }

        return $this->twig->render($response, 'supprimer.html.twig');
    }

    public function modifier(Request $request, Response $response): Response
{
    if ($request->getMethod() === 'POST') {
        $data = $request->getParsedBody();

        // Recherche par identifiant uniquement
        $utilisateur = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->findOneBy(['identifiant' => $data['identifiant']]);

        if (!$utilisateur) {
            return $this->twig->render($response, 'modifier.html.twig', [
                'erreur' => 'Aucun utilisateur trouvé avec ces informations.'
            ]);
        }

        if (!empty($data['nom']))         $utilisateur->setNom($data['nom']);
        if (!empty($data['prenom']))      $utilisateur->setPrenom($data['prenom']);
        if (!empty($data['email']))       $utilisateur->setEmail($data['email']);
        if (!empty($data['identifiant'])) $utilisateur->setIdentifiant($data['identifiant']);
        if (!empty($data['role']))        $utilisateur->setRole($data['role']);

        $this->entityManager->flush();

        return $response->withHeader('Location', '/utilisateurs')->withStatus(302);
    }

    $campus = $this->entityManager->getRepository(Campus::class)->findBy([], ['ville' => 'ASC']);
    return $this->twig->render($response, 'modifier.html.twig', ['campus' => $campus]);
}
}