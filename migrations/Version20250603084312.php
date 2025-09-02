<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603084312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE friendships (id INT AUTO_INCREMENT NOT NULL, user1_id INT DEFAULT NULL, user2_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_E0A8B7CA56AE248B (user1_id), INDEX IDX_E0A8B7CA441B8B65 (user2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendships ADD CONSTRAINT FK_E0A8B7CA56AE248B FOREIGN KEY (user1_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendships ADD CONSTRAINT FK_E0A8B7CA441B8B65 FOREIGN KEY (user2_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction ADD is_split TINYINT(1) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE friendships DROP FOREIGN KEY FK_E0A8B7CA56AE248B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE friendships DROP FOREIGN KEY FK_E0A8B7CA441B8B65
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE friendships
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transaction DROP is_split
        SQL);
    }
}
