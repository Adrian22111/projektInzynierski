<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321203736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, status_name VARCHAR(255) NOT NULL, refers_to LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adoption_case ADD status_id INT NOT NULL, ADD archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE adoption_case ADD CONSTRAINT FK_CC490256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_CC490256BF700BD ON adoption_case (status_id)');
        $this->addSql('ALTER TABLE documents ADD status_id INT NOT NULL, ADD archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B072886BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_A2B072886BF700BD ON documents (status_id)');
        $this->addSql('ALTER TABLE dog ADD status_id INT NOT NULL, ADD archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE dog ADD CONSTRAINT FK_812C397D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_812C397D6BF700BD ON dog (status_id)');
        $this->addSql('ALTER TABLE post ADD status_id INT NOT NULL, ADD archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D6BF700BD ON post (status_id)');
        $this->addSql('ALTER TABLE user ADD status_id INT NOT NULL, ADD archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496BF700BD ON user (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_case DROP FOREIGN KEY FK_CC490256BF700BD');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B072886BF700BD');
        $this->addSql('ALTER TABLE dog DROP FOREIGN KEY FK_812C397D6BF700BD');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D6BF700BD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496BF700BD');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP INDEX IDX_A2B072886BF700BD ON documents');
        $this->addSql('ALTER TABLE documents DROP status_id, DROP archived');
        $this->addSql('DROP INDEX IDX_5A8A6C8D6BF700BD ON post');
        $this->addSql('ALTER TABLE post DROP status_id, DROP archived');
        $this->addSql('DROP INDEX IDX_812C397D6BF700BD ON dog');
        $this->addSql('ALTER TABLE dog DROP status_id, DROP archived');
        $this->addSql('DROP INDEX IDX_CC490256BF700BD ON adoption_case');
        $this->addSql('ALTER TABLE adoption_case DROP status_id, DROP archived');
        $this->addSql('DROP INDEX IDX_8D93D6496BF700BD ON user');
        $this->addSql('ALTER TABLE user DROP status_id, DROP archived');
    }
}
