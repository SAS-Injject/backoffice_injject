<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927085146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, writen_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_important TINYINT(1) NOT NULL, seen INT NOT NULL, is_published TINYINT(1) NOT NULL, published_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', thumbnail_name VARCHAR(255) NOT NULL, thumbnail_file LONGTEXT NOT NULL, thumbnail_alt VARCHAR(255) NOT NULL, thumbnail_legend VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_articles_categories (articles_id INT NOT NULL, articles_categories_id INT NOT NULL, INDEX IDX_7E95DEBF1EBAF6CC (articles_id), INDEX IDX_7E95DEBF66494F6B (articles_categories_id), PRIMARY KEY(articles_id, articles_categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_users (articles_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_FC618D1D1EBAF6CC (articles_id), INDEX IDX_FC618D1D67B3B43D (users_id), PRIMARY KEY(articles_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_categories (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, acount VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, logo_name VARCHAR(255) NOT NULL, logo_file LONGTEXT NOT NULL, logo_alt VARCHAR(255) NOT NULL, logo_legend VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE realization (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, period DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', duration VARCHAR(255) NOT NULL, is_published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE realization_realization_categories (realization_id INT NOT NULL, realization_categories_id INT NOT NULL, INDEX IDX_EA68E901A26530A (realization_id), INDEX IDX_EA68E90C8CFD71D (realization_categories_id), PRIMARY KEY(realization_id, realization_categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE realization_categories (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, account VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE realization_photo (id INT AUTO_INCREMENT NOT NULL, realization_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, file LONGTEXT NOT NULL, alt VARCHAR(255) NOT NULL, legend VARCHAR(255) NOT NULL, INDEX IDX_43BED1D91A26530A (realization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles_articles_categories ADD CONSTRAINT FK_7E95DEBF1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_articles_categories ADD CONSTRAINT FK_7E95DEBF66494F6B FOREIGN KEY (articles_categories_id) REFERENCES articles_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_users ADD CONSTRAINT FK_FC618D1D1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_users ADD CONSTRAINT FK_FC618D1D67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE realization_realization_categories ADD CONSTRAINT FK_EA68E901A26530A FOREIGN KEY (realization_id) REFERENCES realization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE realization_realization_categories ADD CONSTRAINT FK_EA68E90C8CFD71D FOREIGN KEY (realization_categories_id) REFERENCES realization_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE realization_photo ADD CONSTRAINT FK_43BED1D91A26530A FOREIGN KEY (realization_id) REFERENCES realization (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles_articles_categories DROP FOREIGN KEY FK_7E95DEBF1EBAF6CC');
        $this->addSql('ALTER TABLE articles_articles_categories DROP FOREIGN KEY FK_7E95DEBF66494F6B');
        $this->addSql('ALTER TABLE articles_users DROP FOREIGN KEY FK_FC618D1D1EBAF6CC');
        $this->addSql('ALTER TABLE articles_users DROP FOREIGN KEY FK_FC618D1D67B3B43D');
        $this->addSql('ALTER TABLE realization_realization_categories DROP FOREIGN KEY FK_EA68E901A26530A');
        $this->addSql('ALTER TABLE realization_realization_categories DROP FOREIGN KEY FK_EA68E90C8CFD71D');
        $this->addSql('ALTER TABLE realization_photo DROP FOREIGN KEY FK_43BED1D91A26530A');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_articles_categories');
        $this->addSql('DROP TABLE articles_users');
        $this->addSql('DROP TABLE articles_categories');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE realization');
        $this->addSql('DROP TABLE realization_realization_categories');
        $this->addSql('DROP TABLE realization_categories');
        $this->addSql('DROP TABLE realization_photo');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
