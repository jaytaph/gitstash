<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160227194851 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE repository ADD owner_id INT DEFAULT NULL, ADD description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE repository ADD CONSTRAINT FK_5CFE57CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5CFE57CD7E3C61F9 ON repository (owner_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE repository DROP FOREIGN KEY FK_5CFE57CD7E3C61F9');
        $this->addSql('DROP INDEX IDX_5CFE57CD7E3C61F9 ON repository');
        $this->addSql('ALTER TABLE repository DROP owner_id, DROP description');
    }
}
