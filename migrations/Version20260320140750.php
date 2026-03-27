<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260320140750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('entreprises');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('nom', 'string', ['length' => 255]);
        $table->addColumn('secteur', 'string', ['length' => 100, 'notnull' => false]);
        $table->addColumn('statut', 'string', ['length' => 50, 'notnull' => false]);
        $table->addColumn('created_at', 'datetime_immutable');
        $table->setPrimaryKey(['id']);

     
        $table = $schema->createTable('offres');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('titre', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('domaine', 'string', ['length' => 100, 'notnull' => false]);
        $table->addColumn('localisation', 'string', ['length' => 150, 'notnull' => false]);
        $table->addColumn('created_at', 'datetime_immutable');
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        
        $schema->dropTableIfExists('offres');
        $schema->dropTableIfExists('entreprises');
    }
}
