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
    'entreprise',
    'offre_detail'
];

// Si la page n'existe pas → accueil
if (!in_array($page, $allowedPages)) {
    $page = 'accueil';
}

if ($page ==='offre_detail'){
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $offers = [
        1 => [
            'entreprise'   => 'Boston Dynamics',
            'intitule'     => 'Stagiaire Robotique',
            'localisation' => 'Boston',
            'employeur'    => 'Marc Raibert',
            'remuneration' => '3050 $',
            'description'  => 'Wake up samurai, we have a robot to build.',
            'duree'        => '6 mois',
            'type'         => 'Stage temps plein',
        ],
        2 => [
            'entreprise'   => 'Ferrari',
            'intitule'     => 'Stagiaire Ingénierie',
            'localisation' => 'Maranello',
            'employeur'    => 'Enzo Ferrari',
            'remuneration' => '330 €',
            'description'  => 'Je ne peux plus conduire, mais j\'ai encore plein d\'idées.',
            'duree'        => '4 mois',
            'type'         => 'Stage temps plein',
        ],
        3 => [
            'entreprise'   => 'Google',
            'intitule'     => 'Stagiaire Développeur Backend',
            'localisation' => 'Paris',
            'employeur'    => 'Sundar Pichai',
            'remuneration' => '1200 €',
            'description'  => 'Travail sur des microservices en Go et Java.',
            'duree'        => '6 mois',
            'type'         => 'Stage temps plein',
        ],
        4 => [
            'entreprise'   => 'Ubisoft',
            'intitule'     => 'Stagiaire Gameplay',
            'localisation' => 'Montreuil',
            'employeur'    => 'Yves Guillemot',
            'remuneration' => '1000 €',
            'description'  => 'Prototypage de mécaniques de jeu innovantes.',
            'duree'        => '6 mois',
            'type'         => 'Stage temps plein',
        ],
        5 => [
            'entreprise'   => 'Tesla',
            'intitule'     => 'Stagiaire Ingénieur Batterie',
            'localisation' => 'Berlin',
            'employeur'    => 'Elon Musk',
            'remuneration' => '1500 $',
            'description'  => 'Optimisation de la performance énergétique.',
            'duree'        => '6 mois',
            'type'         => 'Stage temps plein',
        ],
];
 if (!isset($offers[$id])){
    header('Location: ?page=offre');
    exit;
 }
 echo $twig->render ('offre_detail.html.twig',[
    'offre' => $offers[$id],
 ]);
 exit;
}

// Afficher la page
echo $twig->render($page . '.html.twig');
