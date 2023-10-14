<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012172815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP CONSTRAINT fk_b723af331d5e8c8f');
        $this->addSql('ALTER TABLE mark_matiere DROP CONSTRAINT fk_f186c1f34290f12b');
        $this->addSql('ALTER TABLE mark_matiere DROP CONSTRAINT fk_f186c1f3f46cd258');
        $this->addSql('ALTER TABLE student_matiere DROP CONSTRAINT fk_7d22e2b2f46cd258');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT fk_b723af338f5ea509');
        $this->addSql('DROP SEQUENCE exam_location_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mark_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE matiere_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE classe_id_seq CASCADE');
        $this->addSql('DROP TABLE exam_location');
        $this->addSql('DROP TABLE mark');
        $this->addSql('DROP TABLE mark_matiere');
        $this->addSql('DROP TABLE student_matiere');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP INDEX idx_b723af331d5e8c8f');
        $this->addSql('DROP INDEX idx_b723af338f5ea509');
        $this->addSql('ALTER TABLE student DROP classe_id');
        $this->addSql('ALTER TABLE student DROP exam_location_id');
        $this->addSql('ALTER TABLE student RENAME COLUMN name TO firstname');
        $this->addSql('ALTER TABLE student RENAME COLUMN fanampiny TO lastname');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE exam_location_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mark_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE matiere_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE classe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exam_location (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mark (id INT NOT NULL, mark DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mark_matiere (mark_id INT NOT NULL, matiere_id INT NOT NULL, PRIMARY KEY(mark_id, matiere_id))');
        $this->addSql('CREATE INDEX idx_f186c1f3f46cd258 ON mark_matiere (matiere_id)');
        $this->addSql('CREATE INDEX idx_f186c1f34290f12b ON mark_matiere (mark_id)');
        $this->addSql('CREATE TABLE student_matiere (student_id INT NOT NULL, matiere_id INT NOT NULL, PRIMARY KEY(student_id, matiere_id))');
        $this->addSql('CREATE INDEX idx_7d22e2b2f46cd258 ON student_matiere (matiere_id)');
        $this->addSql('CREATE INDEX idx_7d22e2b2cb944f1a ON student_matiere (student_id)');
        $this->addSql('CREATE TABLE matiere (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE classe (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE mark_matiere ADD CONSTRAINT fk_f186c1f34290f12b FOREIGN KEY (mark_id) REFERENCES mark (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mark_matiere ADD CONSTRAINT fk_f186c1f3f46cd258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_matiere ADD CONSTRAINT fk_7d22e2b2cb944f1a FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_matiere ADD CONSTRAINT fk_7d22e2b2f46cd258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD classe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD exam_location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student RENAME COLUMN firstname TO name');
        $this->addSql('ALTER TABLE student RENAME COLUMN lastname TO fanampiny');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT fk_b723af338f5ea509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT fk_b723af331d5e8c8f FOREIGN KEY (exam_location_id) REFERENCES exam_location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b723af331d5e8c8f ON student (exam_location_id)');
        $this->addSql('CREATE INDEX idx_b723af338f5ea509 ON student (classe_id)');
    }
}
