<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220519231044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE especie_marcador (especie_id INT NOT NULL, marcador_id INT NOT NULL, INDEX IDX_C13CE141E70ED95B (especie_id), INDEX IDX_C13CE141B323D722 (marcador_id), PRIMARY KEY(especie_id, marcador_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE especie_marcador ADD CONSTRAINT FK_C13CE141E70ED95B FOREIGN KEY (especie_id) REFERENCES especie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE especie_marcador ADD CONSTRAINT FK_C13CE141B323D722 FOREIGN KEY (marcador_id) REFERENCES marcador (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE especie_marcador');
    }
}
