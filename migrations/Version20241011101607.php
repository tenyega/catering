<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241011101607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ADD menu_itemid_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DF311CD83 FOREIGN KEY (menu_itemid_id) REFERENCES menu_item (id)');
        $this->addSql('CREATE INDEX IDX_6D28840DF311CD83 ON payment (menu_itemid_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DF311CD83');
        $this->addSql('DROP INDEX IDX_6D28840DF311CD83 ON payment');
        $this->addSql('ALTER TABLE payment DROP menu_itemid_id');
    }
}
