<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201212104703 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA5AE01D98BB94C5');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP INDEX UNIQ_BA5AE01D98BB94C5 ON blog_post');
        $this->addSql('ALTER TABLE blog_post ADD thumbnail VARCHAR(100) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP hero_image_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, alt VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, thumbnail VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE blog_post ADD hero_image_id INT DEFAULT NULL, DROP thumbnail, DROP updated_at');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA5AE01D98BB94C5 FOREIGN KEY (hero_image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA5AE01D98BB94C5 ON blog_post (hero_image_id)');
    }
}
