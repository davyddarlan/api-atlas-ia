<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610152001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cladograma (id INT AUTO_INCREMENT NOT NULL, reino_id INT DEFAULT NULL, filo_id INT DEFAULT NULL, divisao_id INT DEFAULT NULL, classe_id INT DEFAULT NULL, subclasse_id INT DEFAULT NULL, ordem_id INT DEFAULT NULL, familia_id INT DEFAULT NULL, subfamilia_id INT DEFAULT NULL, genero_id INT DEFAULT NULL, INDEX IDX_B0C0F690A165460F (reino_id), INDEX IDX_B0C0F690FC769908 (filo_id), INDEX IDX_B0C0F6906CF6B047 (divisao_id), INDEX IDX_B0C0F6908F5EA509 (classe_id), INDEX IDX_B0C0F6901B310E30 (subclasse_id), INDEX IDX_B0C0F69085E52AF1 (ordem_id), INDEX IDX_B0C0F690D02563A3 (familia_id), INDEX IDX_B0C0F6908FB48400 (subfamilia_id), INDEX IDX_B0C0F690BCE7B795 (genero_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F690A165460F FOREIGN KEY (reino_id) REFERENCES reino (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F690FC769908 FOREIGN KEY (filo_id) REFERENCES filo (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F6906CF6B047 FOREIGN KEY (divisao_id) REFERENCES divisao (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F6908F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F6901B310E30 FOREIGN KEY (subclasse_id) REFERENCES sub_classe (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F69085E52AF1 FOREIGN KEY (ordem_id) REFERENCES ordem (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F690D02563A3 FOREIGN KEY (familia_id) REFERENCES familia (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F6908FB48400 FOREIGN KEY (subfamilia_id) REFERENCES sub_familia (id)');
        $this->addSql('ALTER TABLE cladograma ADD CONSTRAINT FK_B0C0F690BCE7B795 FOREIGN KEY (genero_id) REFERENCES genero (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cladograma');
    }
}
