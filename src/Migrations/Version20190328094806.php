<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190328094806 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C3D069E18');
        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE4773D069E18');
        $this->addSql('ALTER TABLE discipline_level DROP FOREIGN KEY FK_DD8D7F5B5FB14BA7');
        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE4775FB14BA7');
        $this->addSql('DROP TABLE discipline_level');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP INDEX IDX_5E3DE4773D069E18 ON skill');
        $this->addSql('DROP INDEX IDX_5E3DE4775FB14BA7 ON skill');
        $this->addSql('ALTER TABLE skill DROP discipline_level_id, DROP level_id');
        $this->addSql('DROP INDEX IDX_AEDAD51C3D069E18 ON exercise');
        $this->addSql('ALTER TABLE exercise DROP discipline_level_id, DROP level');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE discipline_level (id INT AUTO_INCREMENT NOT NULL, level_id INT DEFAULT NULL, discipline_id INT DEFAULT NULL, INDEX IDX_DD8D7F5BA5522701 (discipline_id), INDEX IDX_DD8D7F5B5FB14BA7 (level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE discipline_level ADD CONSTRAINT FK_DD8D7F5B5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE discipline_level ADD CONSTRAINT FK_DD8D7F5BA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('ALTER TABLE exercise ADD discipline_level_id INT DEFAULT NULL, ADD level VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C3D069E18 FOREIGN KEY (discipline_level_id) REFERENCES discipline_level (id)');
        $this->addSql('CREATE INDEX IDX_AEDAD51C3D069E18 ON exercise (discipline_level_id)');
        $this->addSql('ALTER TABLE skill ADD discipline_level_id INT DEFAULT NULL, ADD level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE4773D069E18 FOREIGN KEY (discipline_level_id) REFERENCES discipline_level (id)');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE4775FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('CREATE INDEX IDX_5E3DE4773D069E18 ON skill (discipline_level_id)');
        $this->addSql('CREATE INDEX IDX_5E3DE4775FB14BA7 ON skill (level_id)');
    }
}
