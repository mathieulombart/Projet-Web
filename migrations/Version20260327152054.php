<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260327150528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprimer les doublons dans la table wishlist';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les doublons en gardant l'entrée la plus ancienne (id le plus petit)
        $this->addSql('DELETE w1 FROM wishlist w1 INNER JOIN wishlist w2 ON w1.offreId = w2.offreId AND w1.id > w2.id');
    }

    public function down(Schema $schema): void
    {
        // Cette migration supprime des données, pas de rollback possible
    }
}