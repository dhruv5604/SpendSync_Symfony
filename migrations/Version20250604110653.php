<?php

    declare(strict_types=1);

    namespace DoctrineMigrations;

    use Doctrine\DBAL\Schema\Schema;
    use Doctrine\Migrations\AbstractMigration;

    /**
     * Auto-generated Migration: Please modify to your needs!
     */
    final class Version20250604110653 extends AbstractMigration
    {
        public function getDescription(): string
        {
            return '';
        }

        public function up(Schema $schema): void
        {
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSql(<<<'SQL'
                ALTER TABLE friendships ADD created_at DATETIME NULL, ADD updated_at DATETIME NULL
            SQL);
            $this->addSql('UPDATE friendships SET created_at = NOW(), updated_at = NOW()');
            
        }

        public function down(Schema $schema): void
        {
            // this down() migration is auto-generated, please modify it to your needs
            $this->addSql(<<<'SQL'
                ALTER TABLE friendships DROP created_at, DROP updated_at
            SQL);
        }
    }
