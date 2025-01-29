<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127200329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ex12_address (id INT AUTO_INCREMENT NOT NULL, address_line VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(20) NOT NULL, country VARCHAR(255) NOT NULL, person_id INT NOT NULL, INDEX IDX_14173E06217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ex12_bank_account (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(255) NOT NULL, balance NUMERIC(10, 2) NOT NULL, person_id INT NOT NULL, UNIQUE INDEX UNIQ_DBB88668B1A4D127 (account_number), UNIQUE INDEX UNIQ_DBB88668217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ex12_person (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, enable TINYINT(1) NOT NULL, birthdate DATETIME NOT NULL, address LONGTEXT NOT NULL, marital_status ENUM(\'single\', \'married\', \'widower\'), UNIQUE INDEX UNIQ_1BB27E0F85E0677 (username), UNIQUE INDEX UNIQ_1BB27E0E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ex12_address ADD CONSTRAINT FK_14173E06217BBB47 FOREIGN KEY (person_id) REFERENCES Ex09persons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ex12_bank_account ADD CONSTRAINT FK_DBB88668217BBB47 FOREIGN KEY (person_id) REFERENCES Ex09persons (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE Ex09persons CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ex12_address DROP FOREIGN KEY FK_14173E06217BBB47');
        $this->addSql('ALTER TABLE ex12_bank_account DROP FOREIGN KEY FK_DBB88668217BBB47');
        $this->addSql('DROP TABLE ex12_address');
        $this->addSql('DROP TABLE ex12_bank_account');
        $this->addSql('DROP TABLE ex12_person');
        $this->addSql('ALTER TABLE Ex09persons CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\') DEFAULT NULL');
    }
}
