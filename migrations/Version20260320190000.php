<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260320190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes manquantes dans entreprises et offres';
    }

    public function up(Schema $schema): void
    {
        // Ajouter ville et description à la table entreprises
        $this->addSql('ALTER TABLE entreprises ADD ville VARCHAR(150) NULL, ADD description TEXT NULL');

        // Ajouter les colonnes manquantes à la table offres
        $this->addSql('ALTER TABLE offres
            ADD type VARCHAR(20) NOT NULL DEFAULT \'stage\',
            ADD remuneration DECIMAL(8,2) NULL,
            ADD duree_semaines INT NULL,
            ADD date_debut DATE NULL,
            ADD is_active TINYINT(1) NOT NULL DEFAULT 1,
            ADD entreprise_id INT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les colonnes ajoutées
        $this->addSql('ALTER TABLE entreprises DROP COLUMN ville, DROP COLUMN description');
        $this->addSql('ALTER TABLE offres
            DROP COLUMN type,
            DROP COLUMN remuneration,
            DROP COLUMN duree_semaines,
            DROP COLUMN date_debut,
            DROP COLUMN is_active,
            DROP COLUMN entreprise_id
        ');
    }
}
