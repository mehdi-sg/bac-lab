<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210182020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fiche (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, is_public TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fiche_version (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, edited_at DATETIME NOT NULL, editor_name VARCHAR(255) NOT NULL, fiche_id INT NOT NULL, INDEX IDX_A71D8819DF522508 (fiche_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE filiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, niveau VARCHAR(255) NOT NULL, actif TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE groupe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, is_public TINYINT NOT NULL, filiere_id INT DEFAULT NULL, createur_id INT NOT NULL, INDEX IDX_4B98C21180AA129 (filiere_id), INDEX IDX_4B98C2173A201E5 (createur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE membre_groupe (id INT AUTO_INCREMENT NOT NULL, role_membre VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, date_joint DATETIME NOT NULL, utilisateur_id INT NOT NULL, groupe_id INT NOT NULL, INDEX IDX_9EB01998FB88E14F (utilisateur_id), INDEX IDX_9EB019987A45358C (groupe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, type_message VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, parent_message_id INT DEFAULT NULL, expediteur_id INT NOT NULL, groupe_id INT NOT NULL, INDEX IDX_B6BD307F14399779 (parent_message_id), INDEX IDX_B6BD307F10335F61 (expediteur_id), INDEX IDX_B6BD307F7A45358C (groupe_id), INDEX idx_message_groupe_created (groupe_id, created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, link VARCHAR(255) DEFAULT NULL, is_read TINYINT NOT NULL, created_at DATETIME NOT NULL, is_seen TINYINT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_6000B0D3FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, niveau VARCHAR(30) NOT NULL, gouvernorat VARCHAR(50) NOT NULL, date_naissance DATE DEFAULT NULL, filiere VARCHAR(100) DEFAULT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_E6D6B297FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active TINYINT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_USER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fiche_version ADD CONSTRAINT FK_A71D8819DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C2173A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019987A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F14399779 FOREIGN KEY (parent_message_id) REFERENCES message (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_version DROP FOREIGN KEY FK_A71D8819DF522508');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21180AA129');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C2173A201E5');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998FB88E14F');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019987A45358C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F14399779');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F7A45358C');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3FB88E14F');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297FB88E14F');
        $this->addSql('DROP TABLE fiche');
        $this->addSql('DROP TABLE fiche_version');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE groupe');
        $this->addSql('DROP TABLE membre_groupe');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
