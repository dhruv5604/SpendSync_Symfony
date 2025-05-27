<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527132710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE expense_account (expense_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_102D1D9EF395DB7B (expense_id), INDEX IDX_102D1D9E9B6B5FBA (account_id), PRIMARY KEY(expense_id, account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE income_account (income_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_C57420DD640ED2C0 (income_id), INDEX IDX_C57420DD9B6B5FBA (account_id), PRIMARY KEY(income_id, account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense_account ADD CONSTRAINT FK_102D1D9EF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense_account ADD CONSTRAINT FK_102D1D9E9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income_account ADD CONSTRAINT FK_C57420DD640ED2C0 FOREIGN KEY (income_id) REFERENCES income (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income_account ADD CONSTRAINT FK_C57420DD9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE expense_account DROP FOREIGN KEY FK_102D1D9EF395DB7B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense_account DROP FOREIGN KEY FK_102D1D9E9B6B5FBA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income_account DROP FOREIGN KEY FK_C57420DD640ED2C0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income_account DROP FOREIGN KEY FK_C57420DD9B6B5FBA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE expense_account
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE income_account
        SQL);
    }
}
