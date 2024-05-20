<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240510001535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collection_category DROP CONSTRAINT fk_f7ccd7f1bde5fe26');
        $this->addSql('DROP INDEX idx_f7ccd7f1bde5fe26');
        $this->addSql('ALTER TABLE collection_category DROP item_collection_id');
        $this->addSql('ALTER TABLE collection_category ALTER name TYPE VARCHAR(45)');
        $this->addSql('ALTER TABLE items_collection ADD collection_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE items_collection ADD CONSTRAINT FK_62555B058EEC0163 FOREIGN KEY (collection_category_id) REFERENCES collection_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_62555B058EEC0163 ON items_collection (collection_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE collection_category ADD item_collection_id INT NOT NULL');
        $this->addSql('ALTER TABLE collection_category ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE collection_category ADD CONSTRAINT fk_f7ccd7f1bde5fe26 FOREIGN KEY (item_collection_id) REFERENCES items_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f7ccd7f1bde5fe26 ON collection_category (item_collection_id)');
        $this->addSql('ALTER TABLE items_collection DROP CONSTRAINT FK_62555B058EEC0163');
        $this->addSql('DROP INDEX IDX_62555B058EEC0163');
        $this->addSql('ALTER TABLE items_collection DROP collection_category_id');
    }
}
