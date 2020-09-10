<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190819123012 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE academic_career (id INT AUTO_INCREMENT NOT NULL, pathway_id INT DEFAULT NULL, grade_id INT DEFAULT NULL, specialism_id INT DEFAULT NULL, profession_id INT DEFAULT NULL, INDEX IDX_59A112D6F3DA7551 (pathway_id), INDEX IDX_59A112D6FE19A1A8 (grade_id), INDEX IDX_59A112D65601140F (specialism_id), UNIQUE INDEX UNIQ_59A112D6FDEF8996 (profession_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE academic_career_discipline_level (academic_career_id INT NOT NULL, discipline_level_id INT NOT NULL, INDEX IDX_B480B06B36606F46 (academic_career_id), INDEX IDX_B480B06B3D069E18 (discipline_level_id), PRIMARY KEY(academic_career_id, discipline_level_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pathway (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profession (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shortname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pathway_specialism (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE academic_career ADD CONSTRAINT FK_59A112D6F3DA7551 FOREIGN KEY (pathway_id) REFERENCES pathway (id)');
        $this->addSql('ALTER TABLE academic_career ADD CONSTRAINT FK_59A112D6FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE academic_career ADD CONSTRAINT FK_59A112D65601140F FOREIGN KEY (specialism_id) REFERENCES pathway_specialism (id)');
        $this->addSql('ALTER TABLE academic_career ADD CONSTRAINT FK_59A112D6FDEF8996 FOREIGN KEY (profession_id) REFERENCES profession (id)');
        $this->addSql('ALTER TABLE academic_career_discipline_level ADD CONSTRAINT FK_B480B06B36606F46 FOREIGN KEY (academic_career_id) REFERENCES academic_career (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE academic_career_discipline_level ADD CONSTRAINT FK_B480B06B3D069E18 FOREIGN KEY (discipline_level_id) REFERENCES discipline_level (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_call ADD CONSTRAINT FK_F3AD54E640C6888C FOREIGN KEY (roll_call_id) REFERENCES roll_call (id)');
        $this->addSql('ALTER TABLE student_call ADD CONSTRAINT FK_F3AD54E6CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE section ADD pathway_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEFF3DA7551 FOREIGN KEY (pathway_id) REFERENCES pathway (id)');
        $this->addSql('CREATE INDEX IDX_2D737AEFF3DA7551 ON section (pathway_id)');
        $this->addSql('ALTER TABLE student_call DROP justification');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE academic_career_discipline_level DROP FOREIGN KEY FK_B480B06B36606F46');
        $this->addSql('ALTER TABLE academic_career DROP FOREIGN KEY FK_59A112D6F3DA7551');
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEFF3DA7551');
        $this->addSql('ALTER TABLE academic_career DROP FOREIGN KEY FK_59A112D6FDEF8996');
        $this->addSql('ALTER TABLE academic_career DROP FOREIGN KEY FK_59A112D65601140F');
        $this->addSql('DROP TABLE academic_career');
        $this->addSql('DROP TABLE academic_career_discipline_level');
        $this->addSql('DROP TABLE pathway');
        $this->addSql('DROP TABLE profession');
        $this->addSql('DROP TABLE pathway_specialism');
        $this->addSql('DROP INDEX IDX_2D737AEFF3DA7551 ON section');
        $this->addSql('ALTER TABLE section DROP pathway_id');
        $this->addSql('ALTER TABLE student_call DROP FOREIGN KEY FK_F3AD54E640C6888C');
        $this->addSql('ALTER TABLE student_call DROP FOREIGN KEY FK_F3AD54E6CB944F1A');
        $this->addSql('ALTER TABLE student_call ADD justification VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
