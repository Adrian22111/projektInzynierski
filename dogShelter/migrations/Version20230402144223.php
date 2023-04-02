<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402144223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adoption_case (id INT AUTO_INCREMENT NOT NULL, dog_id INT NOT NULL, client_id INT NOT NULL, status_id INT NOT NULL, archived TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_CC49025634DFEB (dog_id), INDEX IDX_CC4902519EB6921 (client_id), INDEX IDX_CC490256BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adoption_case_user (adoption_case_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5EC49612B58FCD84 (adoption_case_id), INDEX IDX_5EC49612A76ED395 (user_id), PRIMARY KEY(adoption_case_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, adoption_case_id INT DEFAULT NULL, status_id INT NOT NULL, document_name VARCHAR(255) NOT NULL, document_source VARCHAR(255) NOT NULL, archived TINYINT(1) NOT NULL, INDEX IDX_A2B07288B58FCD84 (adoption_case_id), INDEX IDX_A2B072886BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dog (id INT AUTO_INCREMENT NOT NULL, status_id INT NOT NULL, name VARCHAR(30) NOT NULL, age INT DEFAULT NULL, race VARCHAR(255) DEFAULT NULL, sex VARCHAR(30) DEFAULT NULL, description VARCHAR(1000) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, in_adoption TINYINT(1) NOT NULL, archived TINYINT(1) NOT NULL, INDEX IDX_812C397D6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, post_owner_id INT NOT NULL, status_id INT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(5400) NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, archived TINYINT(1) NOT NULL, INDEX IDX_5A8A6C8DC1D1E858 (post_owner_id), INDEX IDX_5A8A6C8D6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, status_id INT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, description VARCHAR(1000) DEFAULT NULL, facebook_profile VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(20) DEFAULT NULL, profile_image VARCHAR(255) DEFAULT NULL, available TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, archived TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D6496BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_dog (user_id INT NOT NULL, dog_id INT NOT NULL, INDEX IDX_6A3A58F6A76ED395 (user_id), INDEX IDX_6A3A58F6634DFEB (dog_id), PRIMARY KEY(user_id, dog_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adoption_case ADD CONSTRAINT FK_CC49025634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id)');
        $this->addSql('ALTER TABLE adoption_case ADD CONSTRAINT FK_CC4902519EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE adoption_case ADD CONSTRAINT FK_CC490256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE adoption_case_user ADD CONSTRAINT FK_5EC49612B58FCD84 FOREIGN KEY (adoption_case_id) REFERENCES adoption_case (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adoption_case_user ADD CONSTRAINT FK_5EC49612A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288B58FCD84 FOREIGN KEY (adoption_case_id) REFERENCES adoption_case (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B072886BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE dog ADD CONSTRAINT FK_812C397D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC1D1E858 FOREIGN KEY (post_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE user_dog ADD CONSTRAINT FK_6A3A58F6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_dog ADD CONSTRAINT FK_6A3A58F6634DFEB FOREIGN KEY (dog_id) REFERENCES dog (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adoption_case DROP FOREIGN KEY FK_CC49025634DFEB');
        $this->addSql('ALTER TABLE adoption_case DROP FOREIGN KEY FK_CC4902519EB6921');
        $this->addSql('ALTER TABLE adoption_case DROP FOREIGN KEY FK_CC490256BF700BD');
        $this->addSql('ALTER TABLE adoption_case_user DROP FOREIGN KEY FK_5EC49612B58FCD84');
        $this->addSql('ALTER TABLE adoption_case_user DROP FOREIGN KEY FK_5EC49612A76ED395');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288B58FCD84');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B072886BF700BD');
        $this->addSql('ALTER TABLE dog DROP FOREIGN KEY FK_812C397D6BF700BD');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC1D1E858');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D6BF700BD');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496BF700BD');
        $this->addSql('ALTER TABLE user_dog DROP FOREIGN KEY FK_6A3A58F6A76ED395');
        $this->addSql('ALTER TABLE user_dog DROP FOREIGN KEY FK_6A3A58F6634DFEB');
        $this->addSql('DROP TABLE adoption_case');
        $this->addSql('DROP TABLE adoption_case_user');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE dog');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_dog');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
