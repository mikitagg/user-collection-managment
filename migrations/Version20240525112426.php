<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525112426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE custom_item_attribute ADD item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE custom_item_attribute ADD CONSTRAINT FK_DC45CCD1126F525E FOREIGN KEY (item_id) REFERENCES item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DC45CCD1126F525E ON custom_item_attribute (item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE custom_item_attribute DROP CONSTRAINT FK_DC45CCD1126F525E');
        $this->addSql('DROP INDEX IDX_DC45CCD1126F525E');
        $this->addSql('ALTER TABLE custom_item_attribute DROP item_id');
    }
}
