<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230206180321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_posts (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_3ED591DEA76ED395 (user_id), INDEX IDX_3ED591DE4B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite_posts ADD CONSTRAINT FK_3ED591DEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_posts ADD CONSTRAINT FK_3ED591DE4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_posts DROP FOREIGN KEY FK_3ED591DEA76ED395');
        $this->addSql('ALTER TABLE favorite_posts DROP FOREIGN KEY FK_3ED591DE4B89032C');
        $this->addSql('DROP TABLE favorite_posts');
    }
}
