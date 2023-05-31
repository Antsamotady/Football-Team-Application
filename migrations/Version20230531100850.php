<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230531100850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // the non-hashed password is "lalala"
        $this->addSql('INSERT INTO "user" ("id", "roles", "email", "password") VALUES (1, \'[]\', \'test@mail.com\', \'$2y$13$JvcIhnEi69lIVa4G5GDWQuCC/vNVrT2ZEV/1J0OUIVnjblA6Vrq1m\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
