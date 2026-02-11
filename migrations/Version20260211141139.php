<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211141139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chapitre (id_chapitre INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, contenu LONGTEXT NOT NULL, actif TINYINT DEFAULT 1 NOT NULL, ordre INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id_matiere INT NOT NULL, INDEX IDX_8C62B0254E89FE3A (id_matiere), PRIMARY KEY (id_chapitre)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE choix (id_choix INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, est_correct TINYINT NOT NULL, id_question INT NOT NULL, INDEX IDX_4F488091E62CA5DB (id_question), PRIMARY KEY (id_choix)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE filiere (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, niveau VARCHAR(50) NOT NULL, actif TINYINT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE matiere (id_matiere INT AUTO_INCREMENT NOT NULL, nom VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, actif TINYINT DEFAULT 1 NOT NULL, ordre INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, filiere_id INT NOT NULL, INDEX IDX_9014574A180AA129 (filiere_id), PRIMARY KEY (id_matiere)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE question (id_question INT AUTO_INCREMENT NOT NULL, enonce LONGTEXT NOT NULL, type_question VARCHAR(20) NOT NULL, score INT NOT NULL, id_quiz INT NOT NULL, INDEX IDX_B6F7494E2F32E690 (id_quiz), PRIMARY KEY (id_question)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quiz (id_quiz INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, niveau VARCHAR(50) NOT NULL, duree INT NOT NULL, nb_questions INT NOT NULL, date_creation DATETIME NOT NULL, etat TINYINT NOT NULL, id_chapitre INT NOT NULL, id_matiere INT NOT NULL, INDEX IDX_A412FA92DCB95CB0 (id_chapitre), INDEX IDX_A412FA924E89FE3A (id_matiere), PRIMARY KEY (id_quiz)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B0254E89FE3A FOREIGN KEY (id_matiere) REFERENCES matiere (id_matiere)');
        $this->addSql('ALTER TABLE choix ADD CONSTRAINT FK_4F488091E62CA5DB FOREIGN KEY (id_question) REFERENCES question (id_question)');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E2F32E690 FOREIGN KEY (id_quiz) REFERENCES quiz (id_quiz) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92DCB95CB0 FOREIGN KEY (id_chapitre) REFERENCES chapitre (id_chapitre)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA924E89FE3A FOREIGN KEY (id_matiere) REFERENCES matiere (id_matiere)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B0254E89FE3A');
        $this->addSql('ALTER TABLE choix DROP FOREIGN KEY FK_4F488091E62CA5DB');
        $this->addSql('ALTER TABLE matiere DROP FOREIGN KEY FK_9014574A180AA129');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E2F32E690');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92DCB95CB0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA924E89FE3A');
        $this->addSql('DROP TABLE chapitre');
        $this->addSql('DROP TABLE choix');
        $this->addSql('DROP TABLE filiere');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
