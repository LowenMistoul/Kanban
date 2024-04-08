<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404153956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tableau_user (tableau_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5A37B752B062D5BC (tableau_id), INDEX IDX_5A37B752A76ED395 (user_id), PRIMARY KEY(tableau_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tableau_user ADD CONSTRAINT FK_5A37B752B062D5BC FOREIGN KEY (tableau_id) REFERENCES tableau (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tableau_user ADD CONSTRAINT FK_5A37B752A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tableau_user DROP FOREIGN KEY FK_5A37B752B062D5BC');
        $this->addSql('ALTER TABLE tableau_user DROP FOREIGN KEY FK_5A37B752A76ED395');
        $this->addSql('DROP TABLE tableau_user');
    }
}
