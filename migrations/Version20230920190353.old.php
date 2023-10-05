<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920190353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_mark (student_id INT NOT NULL, mark_id INT NOT NULL, PRIMARY KEY(student_id, mark_id))');
        $this->addSql('CREATE INDEX IDX_595789A9CB944F1A ON student_mark (student_id)');
        $this->addSql('CREATE INDEX IDX_595789A94290F12B ON student_mark (mark_id)');
        $this->addSql('ALTER TABLE student_mark ADD CONSTRAINT FK_595789A9CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student_mark ADD CONSTRAINT FK_595789A94290F12B FOREIGN KEY (mark_id) REFERENCES mark (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE student_mark');
    }
}
