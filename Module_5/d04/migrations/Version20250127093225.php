<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127093225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Ex09persons (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, enable TINYINT(1) NOT NULL, birthdate DATETIME NOT NULL, address LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_A2855104F85E0677 (username), UNIQUE INDEX UNIQ_A2855104E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ex09_address (id INT AUTO_INCREMENT NOT NULL, address_line VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(20) NOT NULL, country VARCHAR(255) NOT NULL, person_id INT NOT NULL, INDEX IDX_7980CBE5217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ex09_bank_account (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, person_id INT NOT NULL, UNIQUE INDEX UNIQ_2D7B666DB1A4D127 (account_number), UNIQUE INDEX UNIQ_2D7B666D217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ex09_address ADD CONSTRAINT FK_7980CBE5217BBB47 FOREIGN KEY (person_id) REFERENCES Ex09persons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ex09_bank_account ADD CONSTRAINT FK_2D7B666D217BBB47 FOREIGN KEY (person_id) REFERENCES Ex09persons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE addresses DROP FOREIGN KEY addresses_ibfk_1');
        $this->addSql('ALTER TABLE bank_accounts DROP FOREIGN KEY bank_accounts_ibfk_1');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE bank_accounts');
        $this->addSql('DROP TABLE ex07_person');
        $this->addSql('ALTER TABLE persons DROP marital_status');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, address_line VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, postal_code VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, country VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, INDEX person_id (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bank_accounts (id INT AUTO_INCREMENT NOT NULL, person_id INT NOT NULL, account_number VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, balance NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, UNIQUE INDEX account_number (account_number), INDEX person_id (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ex07_person (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, enable TINYINT(1) NOT NULL, birthdate DATETIME NOT NULL, address TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, UNIQUE INDEX username (username), UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT addresses_ibfk_1 FOREIGN KEY (person_id) REFERENCES persons (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_accounts ADD CONSTRAINT bank_accounts_ibfk_1 FOREIGN KEY (person_id) REFERENCES persons (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ex09_address DROP FOREIGN KEY FK_7980CBE5217BBB47');
        $this->addSql('ALTER TABLE ex09_bank_account DROP FOREIGN KEY FK_2D7B666D217BBB47');
        $this->addSql('DROP TABLE Ex09persons');
        $this->addSql('DROP TABLE ex09_address');
        $this->addSql('DROP TABLE ex09_bank_account');
        $this->addSql('ALTER TABLE persons ADD marital_status ENUM(\'single\', \'married\', \'widower\') DEFAULT \'single\' NOT NULL');
    }
}
