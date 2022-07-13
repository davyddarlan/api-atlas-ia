<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220531150257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meta_dado (id INT AUTO_INCREMENT NOT NULL, multimidia_id INT NOT NULL, nome VARCHAR(255) NOT NULL, valor VARCHAR(255) NOT NULL, INDEX IDX_932FBF76E7F374CC (multimidia_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meta_dado ADD CONSTRAINT FK_932FBF76E7F374CC FOREIGN KEY (multimidia_id) REFERENCES multimidia (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE meta_dado');
    }
}
