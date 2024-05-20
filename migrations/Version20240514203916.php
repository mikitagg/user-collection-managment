<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514203916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE attribute_value_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE item_attribute_value_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE item_attribute_value (id INT NOT NULL, custom_item_attribute_id INT NOT NULL, item_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_448E60208BF3B7B6 ON item_attribute_value (custom_item_attribute_id)');
        $this->addSql('CREATE INDEX IDX_448E6020126F525E ON item_attribute_value (item_id)');
        $this->addSql('ALTER TABLE item_attribute_value ADD CONSTRAINT FK_448E60208BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_attribute_value ADD CONSTRAINT FK_448E6020126F525E FOREIGN KEY (item_id) REFERENCES item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE attribute_value DROP CONSTRAINT fk_fe4fbb828bf3b7b6');
        $this->addSql('DROP TABLE attribute_value');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE item_attribute_value_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE attribute_value_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE attribute_value (id INT NOT NULL, custom_item_attribute_id INT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fe4fbb828bf3b7b6 ON attribute_value (custom_item_attribute_id)');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT fk_fe4fbb828bf3b7b6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_attribute_value DROP CONSTRAINT FK_448E60208BF3B7B6');
        $this->addSql('ALTER TABLE item_attribute_value DROP CONSTRAINT FK_448E6020126F525E');
        $this->addSql('DROP TABLE item_attribute_value');
    }
}
