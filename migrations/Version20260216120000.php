<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260216120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create programs and user_subject_interests tables for orientation system';
    }

    public function up(Schema $schema): void
    {
        // Create programs table
        $this->addSql('CREATE TABLE programs (
            id INT AUTO_INCREMENT NOT NULL, 
            domain_ar VARCHAR(500) DEFAULT NULL, 
            program_name_ar VARCHAR(500) NOT NULL, 
            specialization_ar VARCHAR(500) DEFAULT NULL, 
            program_code VARCHAR(50) DEFAULT NULL, 
            institution_ar VARCHAR(500) DEFAULT NULL, 
            university_ar VARCHAR(500) DEFAULT NULL, 
            bac_type_ar VARCHAR(100) DEFAULT NULL, 
            formula_t VARCHAR(200) NOT NULL, 
            cutoff_2024 NUMERIC(8, 3) DEFAULT NULL, 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE INDEX idx_bac_type ON programs (bac_type_ar)');
        $this->addSql('CREATE INDEX idx_cutoff ON programs (cutoff_2024)');
        $this->addSql('CREATE INDEX idx_university ON programs (university_ar)');

        // Create user_subject_interests table
        $this->addSql('CREATE TABLE user_subject_interests (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            subject_code VARCHAR(20) NOT NULL, 
            interest_score NUMERIC(4, 3) NOT NULL, 
            resource_views INT NOT NULL DEFAULT 0, 
            downloads INT NOT NULL DEFAULT 0, 
            favorites INT NOT NULL DEFAULT 0, 
            comments INT NOT NULL DEFAULT 0, 
            quiz_average_score NUMERIC(4, 2) DEFAULT NULL, 
            quiz_attempts INT NOT NULL DEFAULT 0, 
            time_spent_minutes INT NOT NULL DEFAULT 0, 
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE UNIQUE INDEX user_subject_unique ON user_subject_interests (user_id, subject_code)');
        $this->addSql('CREATE INDEX idx_user ON user_subject_interests (user_id)');
        $this->addSql('CREATE INDEX idx_subject ON user_subject_interests (subject_code)');
        $this->addSql('ALTER TABLE user_subject_interests ADD CONSTRAINT FK_user_subject_interests_user FOREIGN KEY (user_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_subject_interests');
        $this->addSql('DROP TABLE programs');
    }
}