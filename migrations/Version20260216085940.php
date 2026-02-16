<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216085940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre ADD CONSTRAINT FK_8C62B025F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id)');
        $this->addSql('ALTER TABLE choix ADD CONSTRAINT FK_4F488091E62CA5DB FOREIGN KEY (id_question) REFERENCES question (id_question)');
        $this->addSql('ALTER TABLE evaluation_ressource ADD CONSTRAINT FK_419F8EC0FC6CD52A FOREIGN KEY (ressource_id) REFERENCES ressource (id)');
        $this->addSql('ALTER TABLE evaluation_ressource ADD CONSTRAINT FK_419F8EC0FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE fiche ADD CONSTRAINT FK_4C13CC78180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE fiche_favoris ADD CONSTRAINT FK_91329FE8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_favoris ADD CONSTRAINT FK_91329FE8DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_join_requests ADD CONSTRAINT FK_600A465CDF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_join_requests ADD CONSTRAINT FK_600A465CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_join_requests ADD CONSTRAINT FK_600A465C2FFD4FD3 FOREIGN KEY (processed_by_id) REFERENCES utilisateur (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE fiche_moderateurs ADD CONSTRAINT FK_5412B768DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_moderateurs ADD CONSTRAINT FK_5412B768FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fiche_version ADD CONSTRAINT FK_A71D8819DF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C21180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C2173A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE matiere ADD CONSTRAINT FK_9014574A180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB01998FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membre_groupe ADD CONSTRAINT FK_9EB019987A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F14399779 FOREIGN KEY (parent_message_id) REFERENCES message (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FDF522508 FOREIGN KEY (fiche_id) REFERENCES fiche (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7A45358C FOREIGN KEY (groupe_id) REFERENCES groupe (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D36A99F74A FOREIGN KEY (membre_id) REFERENCES membre_groupe (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D37DF4E23 FOREIGN KEY (fiche_join_request_id) REFERENCES fiche_join_requests (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE profil CHANGE date_naissance date_naissance DATE NOT NULL, CHANGE filiere_id filiere_id INT NOT NULL');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E2F32E690 FOREIGN KEY (id_quiz) REFERENCES quiz (id_quiz) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92DCB95CB0 FOREIGN KEY (id_chapitre) REFERENCES chapitre (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA924E89FE3A FOREIGN KEY (id_matiere) REFERENCES matiere (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chapitre DROP FOREIGN KEY FK_8C62B025F46CD258');
        $this->addSql('ALTER TABLE choix DROP FOREIGN KEY FK_4F488091E62CA5DB');
        $this->addSql('ALTER TABLE evaluation_ressource DROP FOREIGN KEY FK_419F8EC0FC6CD52A');
        $this->addSql('ALTER TABLE evaluation_ressource DROP FOREIGN KEY FK_419F8EC0FB88E14F');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78FB88E14F');
        $this->addSql('ALTER TABLE fiche DROP FOREIGN KEY FK_4C13CC78180AA129');
        $this->addSql('ALTER TABLE fiche_favoris DROP FOREIGN KEY FK_91329FE8FB88E14F');
        $this->addSql('ALTER TABLE fiche_favoris DROP FOREIGN KEY FK_91329FE8DF522508');
        $this->addSql('ALTER TABLE fiche_join_requests DROP FOREIGN KEY FK_600A465CDF522508');
        $this->addSql('ALTER TABLE fiche_join_requests DROP FOREIGN KEY FK_600A465CFB88E14F');
        $this->addSql('ALTER TABLE fiche_join_requests DROP FOREIGN KEY FK_600A465C2FFD4FD3');
        $this->addSql('ALTER TABLE fiche_moderateurs DROP FOREIGN KEY FK_5412B768DF522508');
        $this->addSql('ALTER TABLE fiche_moderateurs DROP FOREIGN KEY FK_5412B768FB88E14F');
        $this->addSql('ALTER TABLE fiche_version DROP FOREIGN KEY FK_A71D8819DF522508');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C21180AA129');
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C2173A201E5');
        $this->addSql('ALTER TABLE matiere DROP FOREIGN KEY FK_9014574A180AA129');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB01998FB88E14F');
        $this->addSql('ALTER TABLE membre_groupe DROP FOREIGN KEY FK_9EB019987A45358C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F14399779');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FDF522508');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F7A45358C');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3FB88E14F');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D36A99F74A');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D37DF4E23');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297180AA129');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297FB88E14F');
        $this->addSql('ALTER TABLE profil CHANGE date_naissance date_naissance DATE DEFAULT NULL, CHANGE filiere_id filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E2F32E690');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92DCB95CB0');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA924E89FE3A');
    }
}
