<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210183340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications ADD membre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D36A99F74A FOREIGN KEY (membre_id) REFERENCES membre_groupe (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6000B0D36A99F74A ON notifications (membre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D36A99F74A');
        $this->addSql('DROP INDEX IDX_6000B0D36A99F74A ON notifications');
        $this->addSql('ALTER TABLE notifications DROP membre_id');
    }
}
