<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127211458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Ex09persons CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\')');
        $this->addSql('ALTER TABLE ex12_person CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\')');
        $this->addSql('ALTER TABLE ex13_employee CHANGE birthdate birthdate DATE NOT NULL, CHANGE employed_since employed_since DATE NOT NULL, CHANGE employed_until employed_until DATE DEFAULT NULL, CHANGE hours hours ENUM(\'8\', \'6\', \'4\'), CHANGE position position ENUM(\'manager\', \'account_manager\', \'qa_manager\', \'dev_manager\', \'ceo\', \'coo\', \'backend_dev\', \'frontend_dev\', \'qa_tester\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ex13_employee CHANGE birthdate birthdate DATETIME NOT NULL, CHANGE employed_since employed_since DATETIME NOT NULL, CHANGE employed_until employed_until DATETIME DEFAULT NULL, CHANGE hours hours ENUM(\'8\', \'6\', \'4\') DEFAULT NULL, CHANGE position position ENUM(\'manager\', \'account_manager\', \'qa_manager\', \'dev_manager\', \'ceo\', \'coo\', \'backend_dev\', \'frontend_dev\', \'qa_tester\') DEFAULT NULL');
        $this->addSql('ALTER TABLE ex12_person CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\') DEFAULT NULL');
        $this->addSql('ALTER TABLE Ex09persons CHANGE marital_status marital_status ENUM(\'single\', \'married\', \'widower\') DEFAULT NULL');
    }
}
