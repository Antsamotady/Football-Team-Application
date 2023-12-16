<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231216054210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_subject_subject (student_subject_id INT NOT NULL, subject_id INT NOT NULL, PRIMARY KEY(student_subject_id, subject_id))');
        $this->addSql('CREATE INDEX IDX_2E81AFEEFE13166 ON student_subject_subject (student_subject_id)');
        $this->addSql('CREATE INDEX IDX_2E81AFE23EDC87 ON student_subject_subject (subject_id)');
        $this->addSql('ALTER TABLE student_subject_subject ADD CONSTRAINT FK_2E81AFEEFE13166 FOREIGN KEY (student_subject_id) REFERENCES student_subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_subject_subject ADD CONSTRAINT FK_2E81AFE23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE student_subject_subject');
    }
}
