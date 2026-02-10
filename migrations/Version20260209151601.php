<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209151601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_version ADD CONSTRAINT FK_A71D8819DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id)');
        $this->addSql('ALTER TABLE profil DROP photo, CHANGE date_naissance date_naissance DATE DEFAULT NULL, CHANGE filiere filiere VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur RENAME INDEX uniq_identifier_email TO UNIQ_USER_EMAIL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fiche_version DROP FOREIGN KEY FK_A71D8819DF522508');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297FB88E14F');
        $this->addSql('ALTER TABLE profil ADD photo VARCHAR(255) DEFAULT NULL, CHANGE date_naissance date_naissance VARCHAR(255) DEFAULT NULL, CHANGE filiere filiere VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur RENAME INDEX uniq_user_email TO UNIQ_IDENTIFIER_EMAIL');
    }
}
