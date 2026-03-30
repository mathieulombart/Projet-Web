<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260330120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute campus_id (FK) dans utilisateurs, offres et entreprises. Remplace la colonne campus (string) dans utilisateurs.';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE utilisateurs DROP COLUMN campus');
        $this->addSql('ALTER TABLE utilisateurs ADD campus_id INT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_utilisateurs_campus FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_utilisateurs_campus ON utilisateurs (campus_id)');

        // Offres : ajouter campus_id
        $this->addSql('ALTER TABLE offres ADD campus_id INT NULL');
        $this->addSql('ALTER TABLE offres ADD CONSTRAINT FK_offres_campus FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_offres_campus ON offres (campus_id)');

        // Entreprises : ajouter campus_id
        $this->addSql('ALTER TABLE entreprises ADD campus_id INT NULL');
        $this->addSql('ALTER TABLE entreprises ADD CONSTRAINT FK_entreprises_campus FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_entreprises_campus ON entreprises (campus_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs DROP FOREIGN KEY FK_utilisateurs_campus');
        $this->addSql('DROP INDEX IDX_utilisateurs_campus ON utilisateurs');
        $this->addSql('ALTER TABLE utilisateurs DROP COLUMN campus_id');
        $this->addSql('ALTER TABLE utilisateurs ADD campus VARCHAR(100) NULL');

        $this->addSql('ALTER TABLE offres DROP FOREIGN KEY FK_offres_campus');
        $this->addSql('DROP INDEX IDX_offres_campus ON offres');
        $this->addSql('ALTER TABLE offres DROP COLUMN campus_id');

        $this->addSql('ALTER TABLE entreprises DROP FOREIGN KEY FK_entreprises_campus');
        $this->addSql('DROP INDEX IDX_entreprises_campus ON entreprises');
        $this->addSql('ALTER TABLE entreprises DROP COLUMN campus_id');
    }
}
