<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240509225645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE custom_item_attribute_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE custom_item_attribute (id INT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DC45CCD1BDE5FE26 ON custom_item_attribute (item_collection_id)');
        $this->addSql('ALTER TABLE custom_item_attribute ADD CONSTRAINT FK_DC45CCD1BDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES items_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE item ADD tags VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE items_collection ADD description TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE custom_item_attribute_id_seq CASCADE');
        $this->addSql('ALTER TABLE custom_item_attribute DROP CONSTRAINT FK_DC45CCD1BDE5FE26');
        $this->addSql('DROP TABLE custom_item_attribute');
        $this->addSql('ALTER TABLE items_collection DROP description');
        $this->addSql('ALTER TABLE item DROP name');
        $this->addSql('ALTER TABLE item DROP tags');
    }
}
