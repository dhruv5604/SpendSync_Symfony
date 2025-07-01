<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626134652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE split_transactions DROP FOREIGN KEY FK_C62A46EE6113B373
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C62A46EE6113B373 ON split_transactions
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE split_transactions CHANGE parent_transaction_id_id parent_transaction_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE split_transactions ADD CONSTRAINT FK_C62A46EE311DBF04 FOREIGN KEY (parent_transaction_id) REFERENCES transaction (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C62A46EE311DBF04 ON split_transactions (parent_transaction_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE split_transactions DROP FOREIGN KEY FK_C62A46EE311DBF04
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C62A46EE311DBF04 ON split_transactions
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE split_transactions CHANGE parent_transaction_id parent_transaction_id_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE split_transactions ADD CONSTRAINT FK_C62A46EE6113B373 FOREIGN KEY (parent_transaction_id_id) REFERENCES transaction (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C62A46EE6113B373 ON split_transactions (parent_transaction_id_id)
        SQL);
    }
}
