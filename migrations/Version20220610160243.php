<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610160243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE especie ADD cladograma_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE especie ADD CONSTRAINT FK_FF0814ED42D0F9FB FOREIGN KEY (cladograma_id) REFERENCES cladograma (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF0814ED42D0F9FB ON especie (cladograma_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE especie DROP FOREIGN KEY FK_FF0814ED42D0F9FB');
        $this->addSql('DROP INDEX UNIQ_FF0814ED42D0F9FB ON especie');
        $this->addSql('ALTER TABLE especie DROP cladograma_id');
    }
}
