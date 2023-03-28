<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325130709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE recipe_recipe_type (recipe_id INT NOT NULL, recipe_type_id INT NOT NULL, INDEX IDX_2737439259D8A214 (recipe_id), INDEX IDX_2737439289A882D3 (recipe_type_id), PRIMARY KEY(recipe_id, recipe_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_recipe_type ADD CONSTRAINT FK_2737439259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_recipe_type ADD CONSTRAINT FK_2737439289A882D3 FOREIGN KEY (recipe_type_id) REFERENCES recipe_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe_recipe_type DROP FOREIGN KEY FK_2737439259D8A214');
        $this->addSql('ALTER TABLE recipe_recipe_type DROP FOREIGN KEY FK_2737439289A882D3');
        $this->addSql('DROP TABLE recipe_recipe_type');
    }
}
