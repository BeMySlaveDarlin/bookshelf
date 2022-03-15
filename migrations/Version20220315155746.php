<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220315155746 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_wallet (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, amount BIGINT NOT NULL, currency_code ENUM(\'USD\',\'RUB\') NOT NULL, UNIQUE INDEX UNIQ_193A8922A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_wallet_transaction (id INT AUTO_INCREMENT NOT NULL, user_wallet_id INT NOT NULL, amount BIGINT NOT NULL, type ENUM(\'DEBIT\',\'CREDIT\') NOT NULL, reason ENUM(\'STOCK\',\'TRANSFER\',\'REFUND\') NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX IDX_AB9E1F8971C5AD17 (user_wallet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_wallet ADD CONSTRAINT FK_193A8922A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_wallet_transaction ADD CONSTRAINT FK_AB9E1F8971C5AD17 FOREIGN KEY (user_wallet_id) REFERENCES user_wallet (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_wallet DROP FOREIGN KEY FK_193A8922A76ED395');
        $this->addSql('ALTER TABLE user_wallet_transaction DROP FOREIGN KEY FK_AB9E1F8971C5AD17');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_wallet');
        $this->addSql('DROP TABLE user_wallet_transaction');
    }
}
