<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418172329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, status_name VARCHAR(255) NOT NULL, refers_to LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adoption_case ADD CONSTRAINT FK_CC490256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE adoption_case_user ADD CONSTRAINT FK_5EC49612B58FCD84 FOREIGN KEY (adoption_case_id) REFERENCES adoption_case (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adoption_case_user ADD CONSTRAINT FK_5EC49612A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288B58FCD84 FOREIGN KEY (adoption_case_id) REFERENCES adoption_case (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B072886BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE dog ADD CONSTRAINT FK_812C397D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE post CHANGE post_owner_id post_owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC1D1E858 FOREIGN KEY (post_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE user_dog ADD CONSTRAINT FK_6A3A58F6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_dog ADD CONSTRAINT FK_6A3A58F6634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id) ON DELETE CASCADE');
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
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288B58FCD84');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC1D1E858');
        $this->addSql('ALTER TABLE post CHANGE post_owner_id post_owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_dog DROP FOREIGN KEY FK_6A3A58F6A76ED395');
        $this->addSql('ALTER TABLE user_dog DROP FOREIGN KEY FK_6A3A58F6634DFEB');
        $this->addSql('ALTER TABLE adoption_case_user DROP FOREIGN KEY FK_5EC49612B58FCD84');
        $this->addSql('ALTER TABLE adoption_case_user DROP FOREIGN KEY FK_5EC49612A76ED395');
    }
}
