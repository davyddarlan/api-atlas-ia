<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220424210654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE propriedade_valor (propriedade_id INT NOT NULL, valor_id INT NOT NULL, INDEX IDX_4CDCAF5A9AF410D5 (propriedade_id), INDEX IDX_4CDCAF5A1EBE5BA7 (valor_id), PRIMARY KEY(propriedade_id, valor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE propriedade_valor ADD CONSTRAINT FK_4CDCAF5A9AF410D5 FOREIGN KEY (propriedade_id) REFERENCES propriedade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE propriedade_valor ADD CONSTRAINT FK_4CDCAF5A1EBE5BA7 FOREIGN KEY (valor_id) REFERENCES valor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE propriedade_valor');
    }
}
