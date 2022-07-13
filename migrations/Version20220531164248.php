<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220531164248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meta_dado DROP FOREIGN KEY FK_932FBF76E7F374CC');
        $this->addSql('DROP INDEX IDX_932FBF76E7F374CC ON meta_dado');
        $this->addSql('ALTER TABLE meta_dado DROP multimidia_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meta_dado ADD multimidia_id INT NOT NULL');
        $this->addSql('ALTER TABLE meta_dado ADD CONSTRAINT FK_932FBF76E7F374CC FOREIGN KEY (multimidia_id) REFERENCES multimidia (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_932FBF76E7F374CC ON meta_dado (multimidia_id)');
    }
}
