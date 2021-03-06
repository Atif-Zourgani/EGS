<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190409173448 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE4775FB14BA7');
        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE477A5522701');
        $this->addSql('DROP INDEX IDX_5E3DE4775FB14BA7 ON skill');
        $this->addSql('DROP INDEX IDX_5E3DE477A5522701 ON skill');
        $this->addSql('ALTER TABLE skill DROP discipline_id, DROP level_id');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C5FB14BA7');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51CA5522701');
        $this->addSql('DROP INDEX IDX_AEDAD51C5FB14BA7 ON exercise');
        $this->addSql('DROP INDEX IDX_AEDAD51CA5522701 ON exercise');
        $this->addSql('ALTER TABLE exercise DROP discipline_id, DROP level_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE exercise ADD discipline_id INT DEFAULT NULL, ADD level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51CA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('CREATE INDEX IDX_AEDAD51C5FB14BA7 ON exercise (level_id)');
        $this->addSql('CREATE INDEX IDX_AEDAD51CA5522701 ON exercise (discipline_id)');
        $this->addSql('ALTER TABLE skill ADD discipline_id INT DEFAULT NULL, ADD level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE4775FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE477A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (id)');
        $this->addSql('CREATE INDEX IDX_5E3DE4775FB14BA7 ON skill (level_id)');
        $this->addSql('CREATE INDEX IDX_5E3DE477A5522701 ON skill (discipline_id)');
    }
}
