<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408200050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE descobridor (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C5405ED554BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE especie (id INT AUTO_INCREMENT NOT NULL, nome_cientifico VARCHAR(255) NOT NULL, ano_descoberta VARCHAR(4) DEFAULT NULL, nome_ingles VARCHAR(255) DEFAULT NULL, descricao VARCHAR(140) DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', principal_nome_popular VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FF0814EDF72ABAA9 (nome_cientifico), UNIQUE INDEX UNIQ_FF0814EDD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE especie_nome_popular (especie_id INT NOT NULL, nome_popular_id INT NOT NULL, INDEX IDX_13DC09FDE70ED95B (especie_id), INDEX IDX_13DC09FD547EAD6C (nome_popular_id), PRIMARY KEY(especie_id, nome_popular_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE especie_descobridor (especie_id INT NOT NULL, descobridor_id INT NOT NULL, INDEX IDX_97015DBE70ED95B (especie_id), INDEX IDX_97015DBBFE70364 (descobridor_id), PRIMARY KEY(especie_id, descobridor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nome_popular (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE especie_nome_popular ADD CONSTRAINT FK_13DC09FDE70ED95B FOREIGN KEY (especie_id) REFERENCES especie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE especie_nome_popular ADD CONSTRAINT FK_13DC09FD547EAD6C FOREIGN KEY (nome_popular_id) REFERENCES nome_popular (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE especie_descobridor ADD CONSTRAINT FK_97015DBE70ED95B FOREIGN KEY (especie_id) REFERENCES especie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE especie_descobridor ADD CONSTRAINT FK_97015DBBFE70364 FOREIGN KEY (descobridor_id) REFERENCES descobridor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE especie_descobridor DROP FOREIGN KEY FK_97015DBBFE70364');
        $this->addSql('ALTER TABLE especie_nome_popular DROP FOREIGN KEY FK_13DC09FDE70ED95B');
        $this->addSql('ALTER TABLE especie_descobridor DROP FOREIGN KEY FK_97015DBE70ED95B');
        $this->addSql('ALTER TABLE especie_nome_popular DROP FOREIGN KEY FK_13DC09FD547EAD6C');
        $this->addSql('DROP TABLE descobridor');
        $this->addSql('DROP TABLE especie');
        $this->addSql('DROP TABLE especie_nome_popular');
        $this->addSql('DROP TABLE especie_descobridor');
        $this->addSql('DROP TABLE nome_popular');
    }
}
