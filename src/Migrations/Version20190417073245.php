<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190417073245 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE student_reliability DROP FOREIGN KEY FK_F0B7608BF93C3DF9');
        $this->addSql('DROP TABLE student_reset_points');
        $this->addSql('DROP INDEX IDX_F0B7608BF93C3DF9 ON student_reliability');
        $this->addSql('ALTER TABLE student_reliability DROP reset_points_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE student_reset_points (id INT AUTO_INCREMENT NOT NULL, points INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE student_reliability ADD reset_points_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE student_reliability ADD CONSTRAINT FK_F0B7608BF93C3DF9 FOREIGN KEY (reset_points_id) REFERENCES student_reset_points (id)');
        $this->addSql('CREATE INDEX IDX_F0B7608BF93C3DF9 ON student_reliability (reset_points_id)');
    }
}
