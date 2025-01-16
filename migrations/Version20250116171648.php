<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250116171648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE current_balance ADD subaccount_id INT NOT NULL');
        $this->addSql('ALTER TABLE current_balance ADD CONSTRAINT FK_6F99A7BC4FAB819 FOREIGN KEY (subaccount_id) REFERENCES sub_account (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F99A7BC4FAB819 ON current_balance (subaccount_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE current_balance DROP FOREIGN KEY FK_6F99A7BC4FAB819');
        $this->addSql('DROP INDEX UNIQ_6F99A7BC4FAB819 ON current_balance');
        $this->addSql('ALTER TABLE current_balance DROP subaccount_id');
    }
}
