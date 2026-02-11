<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211175659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fiche_favoris (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, utilisateur_id INT NOT NULL, fiche_id INT NOT NULL, INDEX IDX_91329FE8FB88E14F (utilisateur_id), INDEX IDX_91329FE8DF522508 (fiche_id), UNIQUE INDEX unique_user_fiche_favori (utilisateur_id, fiche_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fiche_join_requests (id INT AUTO_INCREMENT NOT NULL, message VARCHAR(500) DEFAULT NULL, status VARCHAR(20) DEFAULT \'pending\' NOT NULL, created_at DATETIME NOT NULL, processed_at DATETIME DEFAULT NULL, fiche_id INT NOT NULL, utilisateur_id INT NOT NULL, processed_by_id INT DEFAULT NULL, INDEX IDX_600A465CDF522508 (fiche_id), INDEX IDX_600A465CFB88E14F (utilisateur_id), INDEX IDX_600A465C2FFD4FD3 (processed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE fiche_moderateurs (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, is_owner TINYINT NOT NULL, fiche_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_5412B768DF522508 (fiche_id), INDEX IDX_5412B768FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE fiche_favoris ADD CONSTRAINT FK_91329FE8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_favoris ADD CONSTRAINT FK_91329FE8DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_join_requests ADD CONSTRAINT FK_600A465CDF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_join_requests ADD CONSTRAINT FK_600A465CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_join_requests ADD CONSTRAINT FK_600A465C2FFD4FD3 FOREIGN KEY (processed_by_id) REFERENCES utilisateur (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE fiche_moderateurs ADD CONSTRAINT FK_5412B768DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_moderateurs ADD CONSTRAINT FK_5412B768FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche ADD filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('CREATE INDEX IDX_4C13CC78180AA129 ON fiche (filiere_id)');
        $this->addSql('ALTER TABLE notifications ADD fiche_join_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D37DF4E23 FOREIGN KEY (fiche_join_request_id) REFERENCES fiche_join_requests (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6000B0D37DF4E23 ON notifications (fiche_join_request_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_favoris DROP FOREIGN KEY FK_91329FE8FB88E14F');
        $this->addSql('ALTER TABLE fiche_favoris DROP FOREIGN KEY FK_91329FE8DF522508');
        $this->addSql('ALTER TABLE fiche_join_requests DROP FOREIGN KEY FK_600A465CDF522508');
        $this->addSql('ALTER TABLE fiche_join_requests DROP FOREIGN KEY FK_600A465CFB88E14F');
        $this->addSql('ALTER TABLE fiche_join_requests DROP FOREIGN KEY FK_600A465C2FFD4FD3');
        $this->addSql('ALTER TABLE fiche_moderateurs DROP FOREIGN KEY FK_5412B768DF522508');
        $this->addSql('ALTER TABLE fiche_moderateurs DROP FOREIGN KEY FK_5412B768FB88E14F');
        $this->addSql('DROP TABLE fiche_favoris');
        $this->addSql('DROP TABLE fiche_join_requests');
        $this->addSql('DROP TABLE fiche_moderateurs');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78180AA129');
        $this->addSql('DROP INDEX IDX_4C13CC78180AA129 ON fiche');
        $this->addSql('ALTER TABLE fiche DROP filiere_id');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D37DF4E23');
        $this->addSql('DROP INDEX IDX_6000B0D37DF4E23 ON notifications');
        $this->addSql('ALTER TABLE notifications DROP fiche_join_request_id');
    }
}
