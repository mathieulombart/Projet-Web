<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260330000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Lie Candidature et Wishlist à un Utilisateur (ajout de utilisateur_id)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE candidatures ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE candidatures ADD CONSTRAINT FK_candidatures_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_candidatures_utilisateur ON candidatures (utilisateur_id)');

        $this->addSql('ALTER TABLE wishlists ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE wishlists ADD CONSTRAINT FK_wishlists_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_wishlists_utilisateur ON wishlists (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE candidatures DROP FOREIGN KEY FK_candidatures_utilisateur');
        $this->addSql('DROP INDEX IDX_candidatures_utilisateur ON candidatures');
        $this->addSql('ALTER TABLE candidatures DROP COLUMN utilisateur_id');

        $this->addSql('ALTER TABLE wishlists DROP FOREIGN KEY FK_wishlists_utilisateur');
        $this->addSql('DROP INDEX IDX_wishlists_utilisateur ON wishlists');
        $this->addSql('ALTER TABLE wishlists DROP COLUMN utilisateur_id');
    }
}
