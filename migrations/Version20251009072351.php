<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009072351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE taches (id INT AUTO_INCREMENT NOT NULL, employe_id INT NOT NULL, projet_id INT NOT NULL, titre VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, deadline DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_3BF2CD981B65292 (employe_id), INDEX IDX_3BF2CD98C18272 (projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE taches ADD CONSTRAINT FK_3BF2CD981B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE taches ADD CONSTRAINT FK_3BF2CD98C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE taches DROP FOREIGN KEY FK_3BF2CD981B65292');
        $this->addSql('ALTER TABLE taches DROP FOREIGN KEY FK_3BF2CD98C18272');
        $this->addSql('DROP TABLE taches');
    }
}
