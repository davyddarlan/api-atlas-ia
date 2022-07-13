<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220424234043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE multimidia CHANGE titulo titulo VARCHAR(255) DEFAULT NULL, CHANGE nome nome VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2BE797DB54BD530C ON multimidia (nome)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2BE797DB54BD530C ON multimidia');
        $this->addSql('ALTER TABLE multimidia CHANGE titulo titulo VARCHAR(255) NOT NULL, CHANGE nome nome VARCHAR(50) DEFAULT NULL');
    }
}
