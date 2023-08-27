<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230827155946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE mark_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mark (id INT NOT NULL, mark DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mark_matiere (mark_id INT NOT NULL, matiere_id INT NOT NULL, PRIMARY KEY(mark_id, matiere_id))');
        $this->addSql('CREATE INDEX IDX_F186C1F34290F12B ON mark_matiere (mark_id)');
        $this->addSql('CREATE INDEX IDX_F186C1F3F46CD258 ON mark_matiere (matiere_id)');
        $this->addSql('ALTER TABLE mark_matiere ADD CONSTRAINT FK_F186C1F34290F12B FOREIGN KEY (mark_id) REFERENCES mark (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mark_matiere ADD CONSTRAINT FK_F186C1F3F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mark_matiere DROP CONSTRAINT FK_F186C1F34290F12B');
        $this->addSql('DROP SEQUENCE mark_id_seq CASCADE');
        $this->addSql('DROP TABLE mark');
        $this->addSql('DROP TABLE mark_matiere');
    }
}
