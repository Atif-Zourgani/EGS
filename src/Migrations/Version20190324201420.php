<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190324201420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE discipline_discipline_cat');
        $this->addSql('DROP TABLE discipline_level_discipline');
        $this->addSql('DROP TABLE section_grade');
        $this->addSql('DROP TABLE user_category');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE discipline_discipline_cat (discipline_id INT NOT NULL, discipline_cat_id INT NOT NULL, INDEX IDX_E357716EA5522701 (discipline_id), INDEX IDX_E357716E9EED0752 (discipline_cat_id), PRIMARY KEY(discipline_id, discipline_cat_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE discipline_level_discipline (discipline_level_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_402A1EE73D069E18 (discipline_level_id), INDEX IDX_402A1EE7A5522701 (discipline_id), PRIMARY KEY(discipline_level_id, discipline_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE section_grade (section_id INT NOT NULL, grade_id INT NOT NULL, INDEX IDX_CE0DC950D823E37A (section_id), INDEX IDX_CE0DC950FE19A1A8 (grade_id), PRIMARY KEY(section_id, grade_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE discipline_discipline_cat ADD CONSTRAINT FK_E357716E9EED0752 FOREIGN KEY (discipline_cat_id) REFERENCES discipline_cat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline_discipline_cat ADD CONSTRAINT FK_E357716EA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline_level_discipline ADD CONSTRAINT FK_402A1EE73D069E18 FOREIGN KEY (discipline_level_id) REFERENCES discipline_level (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline_level_discipline ADD CONSTRAINT FK_402A1EE7A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_grade ADD CONSTRAINT FK_CE0DC950D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_grade ADD CONSTRAINT FK_CE0DC950FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
    }
}
