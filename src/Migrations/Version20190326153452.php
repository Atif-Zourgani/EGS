<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190326153452 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tstudent_call DROP FOREIGN KEY FK_FD41C219A84234F');
        $this->addSql('CREATE TABLE student_call (id INT AUTO_INCREMENT NOT NULL, roll_call_id INT NOT NULL, student_id INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_F3AD54E640C6888C (roll_call_id), INDEX IDX_F3AD54E6CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_call ADD CONSTRAINT FK_F3AD54E640C6888C FOREIGN KEY (roll_call_id) REFERENCES roll_call (id)');
        $this->addSql('ALTER TABLE student_call ADD CONSTRAINT FK_F3AD54E6CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('DROP TABLE tcall');
        $this->addSql('DROP TABLE tstudent_call');
        $this->addSql('ALTER TABLE roll_call DROP FOREIGN KEY FK_553E0C62CB944F1A');
        $this->addSql('DROP INDEX IDX_553E0C62CB944F1A ON roll_call');
        $this->addSql('ALTER TABLE roll_call DROP student_id, DROP status');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tcall (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, teacher_id INT DEFAULT NULL, created_at DATETIME NOT NULL, half_day VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_27BA413041807E1D (teacher_id), INDEX IDX_27BA4130296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tstudent_call (id INT AUTO_INCREMENT NOT NULL, t_call_id INT NOT NULL, student_id INT NOT NULL, status VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_FD41C219CB944F1A (student_id), INDEX IDX_FD41C219A84234F (t_call_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tcall ADD CONSTRAINT FK_27BA4130296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE tcall ADD CONSTRAINT FK_27BA413041807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE tstudent_call ADD CONSTRAINT FK_FD41C219A84234F FOREIGN KEY (t_call_id) REFERENCES tcall (id)');
        $this->addSql('ALTER TABLE tstudent_call ADD CONSTRAINT FK_FD41C219CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('DROP TABLE student_call');
        $this->addSql('ALTER TABLE roll_call ADD student_id INT DEFAULT NULL, ADD status VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE roll_call ADD CONSTRAINT FK_553E0C62CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('CREATE INDEX IDX_553E0C62CB944F1A ON roll_call (student_id)');
    }
}
