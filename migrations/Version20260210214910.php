<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210214910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD file_path VARCHAR(500) DEFAULT NULL, ADD file_name VARCHAR(100) DEFAULT NULL, ADD fiche_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FDF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B6BD307FDF522508 ON message (fiche_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FDF522508');
        $this->addSql('DROP INDEX IDX_B6BD307FDF522508 ON message');
        $this->addSql('ALTER TABLE message DROP file_path, DROP file_name, DROP fiche_id');
    }
}
