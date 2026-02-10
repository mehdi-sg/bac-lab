<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209164050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre ADD nom VARCHAR(255) NOT NULL, DROP titre, DROP contenu, DROP ordre');
        $this->addSql('ALTER TABLE filiere CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE niveau niveau VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE matiere DROP description, DROP ordre, CHANGE nom nom VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre ADD titre VARCHAR(150) NOT NULL, ADD contenu LONGTEXT NOT NULL, ADD ordre INT DEFAULT NULL, DROP nom');
        $this->addSql('ALTER TABLE filiere CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE niveau niveau VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE matiere ADD description LONGTEXT DEFAULT NULL, ADD ordre INT DEFAULT NULL, CHANGE nom nom VARCHAR(120) NOT NULL');
    }
}
