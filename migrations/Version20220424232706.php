<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220424232706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE multimidia ADD especie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE multimidia ADD CONSTRAINT FK_2BE797DBE70ED95B FOREIGN KEY (especie_id) REFERENCES especie (id)');
        $this->addSql('CREATE INDEX IDX_2BE797DBE70ED95B ON multimidia (especie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE multimidia DROP FOREIGN KEY FK_2BE797DBE70ED95B');
        $this->addSql('DROP INDEX IDX_2BE797DBE70ED95B ON multimidia');
        $this->addSql('ALTER TABLE multimidia DROP especie_id');
    }
}
