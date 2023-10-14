<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231014062759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE subject_classe');
        $this->addSql('ALTER TABLE subject ADD score DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE subject_classe (subject_id INT NOT NULL, classe_id INT NOT NULL, PRIMARY KEY(subject_id, classe_id))');
        $this->addSql('CREATE INDEX idx_79e76b18f5ea509 ON subject_classe (classe_id)');
        $this->addSql('CREATE INDEX idx_79e76b123edc87 ON subject_classe (subject_id)');
        $this->addSql('ALTER TABLE subject_classe ADD CONSTRAINT fk_79e76b123edc87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_classe ADD CONSTRAINT fk_79e76b18f5ea509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject DROP score');
    }
}
