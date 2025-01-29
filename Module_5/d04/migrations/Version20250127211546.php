<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127211546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ex13_employee (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birthdate DATE NOT NULL, active TINYINT(1) NOT NULL, employed_since DATE NOT NULL, employed_until DATE DEFAULT NULL, hours ENUM(\'8\', \'6\', \'4\'), salary INT NOT NULL, position ENUM(\'manager\', \'account_manager\', \'qa_manager\', \'dev_manager\', \'ceo\', \'coo\', \'backend_dev\', \'frontend_dev\', \'qa_tester\'), manager_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_C198514DE7927C74 (email), INDEX IDX_C198514D783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ex13_employee ADD CONSTRAINT FK_C198514D783E3463 FOREIGN KEY (manager_id) REFERENCES ex13_employee (id)');
        $this->addSql('ALTER TABLE Ex09persons CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\')');
        $this->addSql('ALTER TABLE ex12_person CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ex13_employee DROP FOREIGN KEY FK_C198514D783E3463');
        $this->addSql('DROP TABLE ex13_employee');
        $this->addSql('ALTER TABLE ex12_person CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\') DEFAULT NULL');
        $this->addSql('ALTER TABLE Ex09persons CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\') DEFAULT NULL');
    }
}
