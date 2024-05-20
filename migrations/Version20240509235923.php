<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240509235923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE collection_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE collection_category (id INT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F7CCD7F1BDE5FE26 ON collection_category (item_collection_id)');
        $this->addSql('ALTER TABLE collection_category ADD CONSTRAINT FK_F7CCD7F1BDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES items_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE collection_category_id_seq CASCADE');
        $this->addSql('ALTER TABLE collection_category DROP CONSTRAINT FK_F7CCD7F1BDE5FE26');
        $this->addSql('DROP TABLE collection_category');
    }
}
