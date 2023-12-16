<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231216055431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE teacher_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE teacher (id INT NOT NULL, firstname VARCHAR(64) NOT NULL, lastname VARCHAR(64) DEFAULT NULL, gender VARCHAR(11) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE teacher_classe (teacher_id INT NOT NULL, classe_id INT NOT NULL, PRIMARY KEY(teacher_id, classe_id))');
        $this->addSql('CREATE INDEX IDX_9A0C6D6341807E1D ON teacher_classe (teacher_id)');
        $this->addSql('CREATE INDEX IDX_9A0C6D638F5EA509 ON teacher_classe (classe_id)');
        $this->addSql('ALTER TABLE teacher_classe ADD CONSTRAINT FK_9A0C6D6341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE teacher_classe ADD CONSTRAINT FK_9A0C6D638F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE teacher_classe DROP CONSTRAINT FK_9A0C6D6341807E1D');
        $this->addSql('DROP SEQUENCE teacher_id_seq CASCADE');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE teacher_classe');
    }
}
