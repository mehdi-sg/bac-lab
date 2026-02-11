<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211154537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire_ressource DROP FOREIGN KEY `FK_1C08CA73FB88E14F`');
        $this->addSql('ALTER TABLE commentaire_ressource DROP FOREIGN KEY `FK_1C08CA73FC6CD52A`');
        $this->addSql('ALTER TABLE favori_ressource DROP FOREIGN KEY `FK_98E1FD5CFB88E14F`');
        $this->addSql('ALTER TABLE favori_ressource DROP FOREIGN KEY `FK_98E1FD5CFC6CD52A`');
        $this->addSql('DROP TABLE commentaire_ressource');
        $this->addSql('DROP TABLE favori_ressource');
        $this->addSql('ALTER TABLE evaluation_ressource ADD commentaire LONGTEXT DEFAULT NULL, ADD est_favori TINYINT NOT NULL, ADD est_signale TINYINT NOT NULL, ADD date_commentaire DATETIME DEFAULT NULL, ADD date_favori DATETIME DEFAULT NULL, CHANGE note note INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ressource CHANGE date_ajout date_ajout DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire_ressource (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_commentaire DATETIME NOT NULL, est_signale TINYINT NOT NULL, ressource_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_1C08CA73FC6CD52A (ressource_id), INDEX IDX_1C08CA73FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE favori_ressource (id INT AUTO_INCREMENT NOT NULL, date_ajout DATETIME NOT NULL, ressource_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_98E1FD5CFC6CD52A (ressource_id), INDEX IDX_98E1FD5CFB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commentaire_ressource ADD CONSTRAINT `FK_1C08CA73FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commentaire_ressource ADD CONSTRAINT `FK_1C08CA73FC6CD52A` FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('ALTER TABLE favori_ressource ADD CONSTRAINT `FK_98E1FD5CFB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE favori_ressource ADD CONSTRAINT `FK_98E1FD5CFC6CD52A` FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('ALTER TABLE evaluation_ressource DROP commentaire, DROP est_favori, DROP est_signale, DROP date_commentaire, DROP date_favori, CHANGE note note INT NOT NULL');
        $this->addSql('ALTER TABLE ressource CHANGE date_ajout date_ajout VARCHAR(255) NOT NULL');
    }
}
