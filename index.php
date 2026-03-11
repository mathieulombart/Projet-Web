<?php

require_once __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Initialisation de Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig   = new Environment($loader);

// Page demandée (défaut : accueil)
$page = $_GET['page'] ?? 'accueil';

// Numéro de page pour la pagination (?p=2)
// On utilise 'p' pour ne pas entrer en conflit avec 'page'
$p = isset($_GET['p']) ? $_GET['p'] : 1;
if (!ctype_digit((string)$p) || $p < 1) {
    $p = 1;
}
$p = (int)$p;

$parPage = 2;

// Données à passer au template
$data = [];

switch ($page) {

    case 'accueil':
        break;

    case 'offre':
        $toutesLesOffres = require __DIR__ . '/data/offres.php';

        $total      = count($toutesLesOffres);
        $totalPages = (int)ceil($total / $parPage);

        if ($p > $totalPages) $p = $totalPages;

        $offset = ($p - 1) * $parPage;

        $data['offres']      = array_slice($toutesLesOffres, $offset, $parPage);
        $data['pageCourante'] = $p;
        $data['totalPages']   = $totalPages;
        break;

    case 'entreprise':
        $toutesLesEntreprises = require __DIR__ . '/data/entreprises.php';

        $total      = count($toutesLesEntreprises);
        $totalPages = (int)ceil($total / $parPage);

        if ($p > $totalPages) $p = $totalPages;

        $offset = ($p - 1) * $parPage;

        $data['entreprises']  = array_slice($toutesLesEntreprises, $offset, $parPage);
        $data['filtres']      = ['Tous', 'Tech & IT', 'Cybersécurité', 'Conseil', 'Industrie', 'Finance', 'Santé'];
        $data['pageCourante'] = $p;
        $data['totalPages']   = $totalPages;
        break;

    case 'profil':
        $data['utilisateur'] = [
            'nom'    => 'Nom Prénom',
            'statut' => 'Étudiant – A2',
            'ecole'  => 'CESI',
            'pilote' => 'Dupont Marie',
            'email'  => 'etudiant@cesi.fr',
            'ville'  => 'Nancy',
        ];
        break;

    case 'wishlist':
        $data['offres_wishlist'] = [
            [
                'intitule'   => 'Développeur Front-End',
                'ville'      => 'Paris',
                'entreprise' => 'Société X',
                'duree'      => 'Stage 6 mois',
                'statut'     => 'En cours',
                'badge'      => 'primary',
            ],
            [
                'intitule'   => 'Technicien Support',
                'ville'      => 'Nancy',
                'entreprise' => 'Entreprise Y',
                'duree'      => 'Stage 3 mois',
                'statut'     => 'Disponible',
                'badge'      => 'success',
            ],
        ];
        break;

    case 'offres_postulees':
        $data['candidatures'] = [
            [
                'intitule'    => 'Stage DevOps',
                'entreprise'  => 'Capgemini',
                'ville'       => 'Paris',
                'duree'       => 'Stage 6 mois',
                'statut'      => 'En attente',
                'badge'       => 'warning',
                'progression' => 40,
                'etapes'      => [
                    ['label' => 'Postulé',    'fait' => true],
                    ['label' => 'En attente', 'fait' => false, 'actif' => true],
                    ['label' => 'Entretien',  'fait' => false],
                    ['label' => 'Décision',   'fait' => false],
                ],
            ],
            [
                'intitule'    => 'Stage Cybersécurité',
                'entreprise'  => 'Thales',
                'ville'       => 'Toulouse',
                'duree'       => 'Stage 6 mois',
                'statut'      => 'Entretien planifié',
                'badge'       => 'info',
                'progression' => 70,
                'etapes'      => [
                    ['label' => 'Postulé',    'fait' => true],
                    ['label' => 'En attente', 'fait' => true],
                    ['label' => 'Entretien',  'fait' => false, 'actif' => true],
                    ['label' => 'Décision',   'fait' => false],
                ],
            ],
        ];
        break;

    default:
        header('Location: ?page=accueil');
        exit;
}

echo $twig->render($page . '.html.twig', $data);