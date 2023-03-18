<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230318171617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_case CHANGE archieved archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE documents CHANGE archieved archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE dog CHANGE archieved archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post CHANGE archieved archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE status CHANGE archieved archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE archieved archived TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE status CHANGE archived archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE documents CHANGE archived archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post CHANGE archived archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE dog CHANGE archived archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE adoption_case CHANGE archived archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE archived archieved TINYINT(1) NOT NULL');
    }
}
