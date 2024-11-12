<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107142210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE current_balance DROP FOREIGN KEY FK_6F99A7BB7A0CC77');
        $this->addSql('DROP INDEX UNIQ_6F99A7BB7A0CC77 ON current_balance');
        $this->addSql('ALTER TABLE current_balance DROP sub_account_id');
        $this->addSql('ALTER TABLE sub_account DROP FOREIGN KEY FK_2EE0A50712A6AAF');
        $this->addSql('DROP INDEX UNIQ_2EE0A50712A6AAF ON sub_account');
        $this->addSql('ALTER TABLE sub_account DROP current_balance_id, DROP bic, DROP blz');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sub_account ADD current_balance_id INT DEFAULT NULL, ADD bic VARCHAR(255) NOT NULL, ADD blz VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sub_account ADD CONSTRAINT FK_2EE0A50712A6AAF FOREIGN KEY (current_balance_id) REFERENCES current_balance (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EE0A50712A6AAF ON sub_account (current_balance_id)');
        $this->addSql('ALTER TABLE current_balance ADD sub_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE current_balance ADD CONSTRAINT FK_6F99A7BB7A0CC77 FOREIGN KEY (sub_account_id) REFERENCES sub_account (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F99A7BB7A0CC77 ON current_balance (sub_account_id)');
    }
}
