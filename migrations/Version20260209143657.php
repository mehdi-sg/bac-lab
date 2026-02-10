<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209143657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapitre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, contenu LONGTEXT NOT NULL, actif TINYINT NOT NULL, ordre INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, matiere_id INT NOT NULL, INDEX IDX_8C62B025F46CD258 (matiere_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fiche (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_public TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fiche_version (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, edited_at DATETIME NOT NULL, editor_name VARCHAR(255) NOT NULL, fiche_id INT NOT NULL, INDEX IDX_A71D8819DF522508 (fiche_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE filiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, niveau VARCHAR(50) NOT NULL, actif TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, actif TINYINT NOT NULL, ordre INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, filiere_id INT NOT NULL, INDEX IDX_9014574A180AA129 (filiere_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE fiche_version ADD CONSTRAINT FK_A71D8819DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id)');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025F46CD258');
        $this->addSql('ALTER TABLE fiche_version DROP FOREIGN KEY FK_A71D8819DF522508');
        $this->addSql('ALTER TABLE matiere DROP FOREIGN KEY FK_9014574A180AA129');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE fiche');
        $this->addSql('DROP TABLE fiche_version');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
