<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190329070005 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE student_evaluation (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, UNIQUE INDEX UNIQ_FEFC4894CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tstudent_skill (id INT AUTO_INCREMENT NOT NULL, teacher_id INT DEFAULT NULL, team_id INT DEFAULT NULL, student_evaluation_id INT DEFAULT NULL, skill_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_FB069EF141807E1D (teacher_id), INDEX IDX_FB069EF1296CD8AE (team_id), INDEX IDX_FB069EF110F0AED8 (student_evaluation_id), UNIQUE INDEX UNIQ_FB069EF15585C142 (skill_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_evaluation ADD CONSTRAINT FK_FEFC4894CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE tstudent_skill ADD CONSTRAINT FK_FB069EF141807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE tstudent_skill ADD CONSTRAINT FK_FB069EF1296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE tstudent_skill ADD CONSTRAINT FK_FB069EF110F0AED8 FOREIGN KEY (student_evaluation_id) REFERENCES student_evaluation (id)');
        $this->addSql('ALTER TABLE tstudent_skill ADD CONSTRAINT FK_FB069EF15585C142 FOREIGN KEY (skill_id) REFERENCES skill (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tstudent_skill DROP FOREIGN KEY FK_FB069EF110F0AED8');
        $this->addSql('DROP TABLE student_evaluation');
        $this->addSql('DROP TABLE tstudent_skill');
    }
}
