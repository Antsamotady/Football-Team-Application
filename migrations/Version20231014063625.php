<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231014063625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student ADD classe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF338F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B723AF338F5EA509 ON student (classe_id)');
        $this->addSql('ALTER TABLE subject ADD student_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7ACB944F1A FOREIGN KEY (student_id) REFERENCES student (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FBCE3E7ACB944F1A ON subject (student_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subject DROP CONSTRAINT FK_FBCE3E7ACB944F1A');
        $this->addSql('DROP INDEX IDX_FBCE3E7ACB944F1A');
        $this->addSql('ALTER TABLE subject DROP student_id');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF338F5EA509');
        $this->addSql('DROP INDEX IDX_B723AF338F5EA509');
        $this->addSql('ALTER TABLE student DROP classe_id');
    }
}
