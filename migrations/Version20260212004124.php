<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212004124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE choix (id_choix INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, est_correct TINYINT NOT NULL, id_question INT NOT NULL, INDEX IDX_4F488091E62CA5DB (id_question), PRIMARY KEY (id_choix)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE question (id_question INT AUTO_INCREMENT NOT NULL, enonce LONGTEXT NOT NULL, type_question VARCHAR(20) NOT NULL, score INT NOT NULL, id_quiz INT NOT NULL, INDEX IDX_B6F7494E2F32E690 (id_quiz), PRIMARY KEY (id_question)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE quiz (id_quiz INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, niveau VARCHAR(50) NOT NULL, duree INT NOT NULL, nb_questions INT NOT NULL, date_creation DATETIME NOT NULL, etat TINYINT NOT NULL, id_chapitre INT NOT NULL, id_matiere INT NOT NULL, INDEX IDX_A412FA92DCB95CB0 (id_chapitre), INDEX IDX_A412FA924E89FE3A (id_matiere), PRIMARY KEY (id_quiz)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE choix ADD CONSTRAINT FK_4F488091E62CA5DB FOREIGN KEY (id_question) REFERENCES question (id_question)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E2F32E690 FOREIGN KEY (id_quiz) REFERENCES quiz (id_quiz) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92DCB95CB0 FOREIGN KEY (id_chapitre) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA924E89FE3A FOREIGN KEY (id_matiere) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE fiche ADD utilisateur_id INT DEFAULT NULL, CHANGE is_public is_public TINYINT NOT NULL');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_4C13CC78FB88E14F ON fiche (utilisateur_id)');
        $this->addSql('ALTER TABLE message ADD file_path VARCHAR(500) DEFAULT NULL, ADD file_name VARCHAR(100) DEFAULT NULL, ADD fiche_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FDF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B6BD307FDF522508 ON message (fiche_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE choix DROP FOREIGN KEY FK_4F488091E62CA5DB');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E2F32E690');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92DCB95CB0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA924E89FE3A');
        $this->addSql('DROP TABLE choix');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78FB88E14F');
        $this->addSql('DROP INDEX IDX_4C13CC78FB88E14F ON fiche');
        $this->addSql('ALTER TABLE fiche DROP utilisateur_id, CHANGE is_public is_public TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FDF522508');
        $this->addSql('DROP INDEX IDX_B6BD307FDF522508 ON message');
        $this->addSql('ALTER TABLE message DROP file_path, DROP file_name, DROP fiche_id');
    }
}
