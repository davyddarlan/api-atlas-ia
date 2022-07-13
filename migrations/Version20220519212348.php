<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220519212348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE propriedade_valor DROP FOREIGN KEY FK_4CDCAF5A9AF410D5');
        $this->addSql('ALTER TABLE propriedade_valor DROP FOREIGN KEY FK_4CDCAF5A1EBE5BA7');
        $this->addSql('DROP TABLE propriedade');
        $this->addSql('DROP TABLE propriedade_valor');
        $this->addSql('DROP TABLE valor');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE propriedade (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE propriedade_valor (propriedade_id INT NOT NULL, valor_id INT NOT NULL, INDEX IDX_4CDCAF5A1EBE5BA7 (valor_id), INDEX IDX_4CDCAF5A9AF410D5 (propriedade_id), PRIMARY KEY(propriedade_id, valor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE valor (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE propriedade_valor ADD CONSTRAINT FK_4CDCAF5A1EBE5BA7 FOREIGN KEY (valor_id) REFERENCES valor (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE propriedade_valor ADD CONSTRAINT FK_4CDCAF5A9AF410D5 FOREIGN KEY (propriedade_id) REFERENCES propriedade (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
