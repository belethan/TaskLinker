<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009055443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employe_projet (employe_id INT NOT NULL, projet_id INT NOT NULL, INDEX IDX_3E3387501B65292 (employe_id), INDEX IDX_3E338750C18272 (projet_id), PRIMARY KEY(employe_id, projet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employe_projet ADD CONSTRAINT FK_3E3387501B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe_projet ADD CONSTRAINT FK_3E338750C18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe CHANGE type_Contrat type_contrat VARCHAR(15) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employe_projet DROP FOREIGN KEY FK_3E3387501B65292');
        $this->addSql('ALTER TABLE employe_projet DROP FOREIGN KEY FK_3E338750C18272');
        $this->addSql('DROP TABLE employe_projet');
        $this->addSql('ALTER TABLE employe CHANGE type_contrat type_Contrat VARCHAR(15) NOT NULL');
    }
}
