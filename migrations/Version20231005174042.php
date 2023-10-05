<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005174042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_matiere (student_id INT NOT NULL, matiere_id INT NOT NULL, PRIMARY KEY(student_id, matiere_id))');
        $this->addSql('CREATE INDEX IDX_7D22E2B2CB944F1A ON student_matiere (student_id)');
        $this->addSql('CREATE INDEX IDX_7D22E2B2F46CD258 ON student_matiere (matiere_id)');
        $this->addSql('ALTER TABLE student_matiere ADD CONSTRAINT FK_7D22E2B2CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_matiere ADD CONSTRAINT FK_7D22E2B2F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE student_matiere');
    }
}
