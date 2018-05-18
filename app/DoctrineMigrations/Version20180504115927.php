<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180504115927 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tblProductData (intProductDataId INT UNSIGNED AUTO_INCREMENT NOT NULL, strProductName VARCHAR(50) NOT NULL, strProductDesc VARCHAR(255) NOT NULL, strProductCode VARCHAR(10) NOT NULL, dtmAdded TIME DEFAULT NULL, dtmDiscontinued DATETIME DEFAULT NULL, stmTimestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_2C11248662F10A58 (strProductCode), PRIMARY KEY(intProductDataId)) DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tblProductData');
    }
}
