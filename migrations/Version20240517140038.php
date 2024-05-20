<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517140038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE item_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE item_tag (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE item_tag_item (item_tag_id INT NOT NULL, item_id INT NOT NULL, PRIMARY KEY(item_tag_id, item_id))');
        $this->addSql('CREATE INDEX IDX_9D1952633C2B16DE ON item_tag_item (item_tag_id)');
        $this->addSql('CREATE INDEX IDX_9D195263126F525E ON item_tag_item (item_id)');
        $this->addSql('ALTER TABLE item_tag_item ADD CONSTRAINT FK_9D1952633C2B16DE FOREIGN KEY (item_tag_id) REFERENCES item_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE item_tag_item ADD CONSTRAINT FK_9D195263126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE item_tag_id_seq CASCADE');
        $this->addSql('ALTER TABLE item_tag_item DROP CONSTRAINT FK_9D1952633C2B16DE');
        $this->addSql('ALTER TABLE item_tag_item DROP CONSTRAINT FK_9D195263126F525E');
        $this->addSql('DROP TABLE item_tag');
        $this->addSql('DROP TABLE item_tag_item');
    }
}
