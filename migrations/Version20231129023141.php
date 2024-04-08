<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231129023141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE colonne (id INT AUTO_INCREMENT NOT NULL, tableau_id INT NOT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, INDEX IDX_65F87C44B062D5BC (tableau_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE colonne ADD CONSTRAINT FK_65F87C44B062D5BC FOREIGN KEY (tableau_id) REFERENCES tableau (id)');
        $this->addSql('ALTER TABLE ticket ADD colonne_id INT NOT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3213EAC9D FOREIGN KEY (colonne_id) REFERENCES colonne (id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3213EAC9D ON ticket (colonne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3213EAC9D');
        $this->addSql('ALTER TABLE colonne DROP FOREIGN KEY FK_65F87C44B062D5BC');
        $this->addSql('DROP TABLE colonne');
        $this->addSql('DROP INDEX IDX_97A0ADA3213EAC9D ON ticket');
        $this->addSql('ALTER TABLE ticket DROP colonne_id');
    }
}
