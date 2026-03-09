<?php
require_once __DIR__ . '/vendor/autoload.php';

// Charger Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader);

// Page demandée
$page = $_GET['page'] ?? 'accueil';

// Pages autorisées (noms EXACTS des fichiers twig sans extension)
$allowedPages = [
    'accueil',
    'profil',
    'offre',
    'offres_postulees',
    'entreprise'
];

// Si la page n'existe pas → accueil
if (!in_array($page, $allowedPages)) {
    $page = 'accueil';
}

// Afficher la page
echo $twig->render($page . '.html.twig');
