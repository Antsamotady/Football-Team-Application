<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012174838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE classe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE student_classe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subject_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE classe (id INT NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE student_classe (id INT NOT NULL, classe_id INT DEFAULT NULL, student_id INT DEFAULT NULL, score DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B167168F5EA509 ON student_classe (classe_id)');
        $this->addSql('CREATE INDEX IDX_1B16716CB944F1A ON student_classe (student_id)');
        $this->addSql('CREATE TABLE subject (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE subject_classe (subject_id INT NOT NULL, classe_id INT NOT NULL, PRIMARY KEY(subject_id, classe_id))');
        $this->addSql('CREATE INDEX IDX_79E76B123EDC87 ON subject_classe (subject_id)');
        $this->addSql('CREATE INDEX IDX_79E76B18F5EA509 ON subject_classe (classe_id)');
        $this->addSql('ALTER TABLE student_classe ADD CONSTRAINT FK_1B167168F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_classe ADD CONSTRAINT FK_1B16716CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_classe ADD CONSTRAINT FK_79E76B123EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_classe ADD CONSTRAINT FK_79E76B18F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE student_classe DROP CONSTRAINT FK_1B167168F5EA509');
        $this->addSql('ALTER TABLE subject_classe DROP CONSTRAINT FK_79E76B18F5EA509');
        $this->addSql('ALTER TABLE subject_classe DROP CONSTRAINT FK_79E76B123EDC87');
        $this->addSql('DROP SEQUENCE classe_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE student_classe_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subject_id_seq CASCADE');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE student_classe');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE subject_classe');
    }
}
