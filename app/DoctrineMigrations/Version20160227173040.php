<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160227173040 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE repo_right (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, repository_id INT DEFAULT NULL, `right` VARCHAR(10) NOT NULL, INDEX IDX_595C6C8FA76ED395 (user_id), INDEX IDX_595C6C8F50C9D4F7 (repository_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE repo_right ADD CONSTRAINT FK_595C6C8FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE repo_right ADD CONSTRAINT FK_595C6C8F50C9D4F7 FOREIGN KEY (repository_id) REFERENCES repository (id)');
        $this->addSql('ALTER TABLE repository DROP FOREIGN KEY FK_5CFE57CD7E3C61F9');
        $this->addSql('DROP INDEX IDX_5CFE57CD7E3C61F9 ON repository');
        $this->addSql('ALTER TABLE repository DROP owner_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE repo_right');
        $this->addSql('ALTER TABLE repository ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE repository ADD CONSTRAINT FK_5CFE57CD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5CFE57CD7E3C61F9 ON repository (owner_id)');
    }
}
