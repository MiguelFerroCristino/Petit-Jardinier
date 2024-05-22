<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522161408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD user_id INT NOT NULL, ADD date DATE NOT NULL, ADD type_client VARCHAR(255) NOT NULL, ADD prix_total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8B27C52BA76ED395 ON devis (user_id)');
        $this->addSql('ALTER TABLE tailler ADD devis_id INT NOT NULL, ADD haie_id INT NOT NULL, ADD longueur NUMERIC(10, 2) NOT NULL, ADD hauteur NUMERIC(10, 2) NOT NULL, DROP tailler');
        $this->addSql('ALTER TABLE tailler ADD CONSTRAINT FK_447D178841DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('ALTER TABLE tailler ADD CONSTRAINT FK_447D1788E7470F2C FOREIGN KEY (haie_id) REFERENCES haie (id)');
        $this->addSql('CREATE INDEX IDX_447D178841DEFADA ON tailler (devis_id)');
        $this->addSql('CREATE INDEX IDX_447D1788E7470F2C ON tailler (haie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BA76ED395');
        $this->addSql('DROP INDEX IDX_8B27C52BA76ED395 ON devis');
        $this->addSql('ALTER TABLE devis DROP user_id, DROP date, DROP type_client, DROP prix_total');
        $this->addSql('ALTER TABLE tailler DROP FOREIGN KEY FK_447D178841DEFADA');
        $this->addSql('ALTER TABLE tailler DROP FOREIGN KEY FK_447D1788E7470F2C');
        $this->addSql('DROP INDEX IDX_447D178841DEFADA ON tailler');
        $this->addSql('DROP INDEX IDX_447D1788E7470F2C ON tailler');
        $this->addSql('ALTER TABLE tailler ADD tailler VARCHAR(255) NOT NULL, DROP devis_id, DROP haie_id, DROP longueur, DROP hauteur');
    }
}
