<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602092231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE income DROP FOREIGN KEY FK_3FA862D09B6B5FBA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income DROP FOREIGN KEY FK_3FA862D0A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA69B6B5FBA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE income
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE expense
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE income (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, account_id INT DEFAULT NULL, amount INT NOT NULL, category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, income_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_3FA862D0A76ED395 (user_id), INDEX IDX_3FA862D09B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE expense (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, account_id INT NOT NULL, amount INT NOT NULL, category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, expense_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2D3A8DA6A76ED395 (user_id), INDEX IDX_2D3A8DA69B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income ADD CONSTRAINT FK_3FA862D09B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE income ADD CONSTRAINT FK_3FA862D0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA69B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
    }
}
