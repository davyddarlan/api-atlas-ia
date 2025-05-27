<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220519190438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE especie ADD estado_conservacao_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE especie ADD CONSTRAINT FK_FF0814ED3A835EE1 FOREIGN KEY (estado_conservacao_id) REFERENCES estado_conservacao (id)');
        $this->addSql('CREATE INDEX IDX_FF0814ED3A835EE1 ON especie (estado_conservacao_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE especie DROP FOREIGN KEY FK_FF0814ED3A835EE1');
        $this->addSql('DROP INDEX IDX_FF0814ED3A835EE1 ON especie');
        $this->addSql('ALTER TABLE especie DROP estado_conservacao_id');
    }
}
