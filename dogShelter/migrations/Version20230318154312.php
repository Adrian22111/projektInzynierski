<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230318154312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_case ADD archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE documents ADD archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE dog ADD archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post ADD archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE status ADD archieved TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD archieved TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE status DROP archieved');
        $this->addSql('ALTER TABLE documents DROP archieved');
        $this->addSql('ALTER TABLE post DROP archieved');
        $this->addSql('ALTER TABLE dog DROP archieved');
        $this->addSql('ALTER TABLE adoption_case DROP archieved');
        $this->addSql('ALTER TABLE user DROP archieved');
    }
}
