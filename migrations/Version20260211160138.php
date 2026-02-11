<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211160138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // First, add the new categorie column
        $this->addSql('ALTER TABLE ressource ADD categorie VARCHAR(100) DEFAULT NULL');
        
        // Migrate data: copy type names to categorie column
        $this->addSql('UPDATE ressource r INNER JOIN type_ressource tr ON r.type_ressource_id = tr.id SET r.categorie = tr.nom');
        
        // Now drop the foreign key and old column
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY `FK_939F45447B2F6F2F`');
        $this->addSql('DROP INDEX IDX_939F45447B2F6F2F ON ressource');
        $this->addSql('ALTER TABLE ressource DROP type_ressource_id');
        
        // Finally drop the type_ressource table
        $this->addSql('DROP TABLE type_ressource');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE type_ressource (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, icone VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ressource ADD type_ressource_id INT NOT NULL, DROP categorie');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT `FK_939F45447B2F6F2F` FOREIGN KEY (type_ressource_id) REFERENCES type_ressource (id)');
        $this->addSql('CREATE INDEX IDX_939F45447B2F6F2F ON ressource (type_ressource_id)');
    }
}
