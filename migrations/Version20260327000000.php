<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260327000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table candidatures';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE candidatures (
            id INT AUTO_INCREMENT NOT NULL,
            offre_id INT NOT NULL,
            etudiant_nom VARCHAR(150) NOT NULL,
            motivation LONGTEXT NOT NULL,
            date_candidature DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            statut VARCHAR(30) NOT NULL DEFAULT \'En attente\',
            PRIMARY KEY(id),
            CONSTRAINT FK_candidatures_offre
                FOREIGN KEY (offre_id) REFERENCES offres (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE candidatures');
    }
}
