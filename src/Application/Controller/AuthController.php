<?php

namespace App\Application\Controller;
namespace App\Domain;

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

    // Affiche le formulaire (GET) ou traite l'inscription (POST)
    public function inscription(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $erreurs = [];

            // Validation basique
            if (empty($data['email']))    $erreurs[] = 'L\'email est obligatoire.';
            if (empty($data['Identifiant']))    $erreurs[] = 'L\'identifiant est obligatoire.';
            if (empty($data['nom']))      $erreurs[] = 'Le nom est obligatoire.';
            if (empty($data['prenom']))   $erreurs[] = 'Le prénom est obligatoire.';
            if (empty($data['password'])) $erreurs[] = 'Le mot de passe est obligatoire.';
            if ($data['password'] !== $data['password-confirm']) {
                $erreurs[] = 'Les mots de passe ne correspondent pas.';
            }

            // Si erreurs → on réaffiche le formulaire avec les messages
            if (!empty($erreurs)) {
                return $this->twig->render($response, 'inscription.html.twig', [
                    'erreurs' => $erreurs
                ]);
            }

            // Hash du mot de passe
            $hash = password_hash($data['password'], PASSWORD_BCRYPT);

            // Création de l'utilisateur
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($data['email']);
            $utilisateur->setIdentifiant($data['Identifiant']);
            $utilisateur->setNom($data['nom']);
            $utilisateur->setPrenom($data['prenom']);
            $utilisateur->setMotDePasse($hash);
            $utilisateur->setRole($data['Type'] ?? Utilisateur::ROLE_ETUDIANT);
            $utilisateur->setPromotion($data['Promo'] ?? '');
            $utilisateur->setCampus($data['zone'] ?? '');

            // Sauvegarde en BDD
            $this->entityManager->persist($utilisateur);
            $this->entityManager->flush();

            // Redirige vers la connexion après inscription
            return $response->withHeader('Location', '/connexion')->withStatus(302);
        }

        // GET → affiche simplement le formulaire
        return $this->twig->render($response, 'inscription.html.twig');
    }
}