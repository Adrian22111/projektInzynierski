<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119024618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_case ADD CONSTRAINT FK_CC4902519EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC4902519EB6921 ON adoption_case (client_id)');
        $this->addSql('ALTER TABLE user ADD status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_case DROP FOREIGN KEY FK_CC4902519EB6921');
        $this->addSql('DROP INDEX UNIQ_CC4902519EB6921 ON adoption_case');
        $this->addSql('ALTER TABLE user DROP status');
    }
}
