<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610151203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8F87BF9654BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE divisao (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A8EB4DA54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE familia (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5E69C24F54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE filo (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6C4ADF0E54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genero (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A000883A54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordem (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_78219E6D54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reino (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_28BD89CC54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_classe (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3DD6F0DA54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_familia (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_21B19EBB54BD530C (nome), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE divisao');
        $this->addSql('DROP TABLE familia');
        $this->addSql('DROP TABLE filo');
        $this->addSql('DROP TABLE genero');
        $this->addSql('DROP TABLE ordem');
        $this->addSql('DROP TABLE reino');
        $this->addSql('DROP TABLE sub_classe');
        $this->addSql('DROP TABLE sub_familia');
    }
}
