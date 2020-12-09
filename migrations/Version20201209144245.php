<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201209144245 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriber_season ADD season_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscriber_season ADD CONSTRAINT FK_CEB18CF04EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('CREATE INDEX IDX_CEB18CF04EC001D1 ON subscriber_season (season_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscriber_season DROP FOREIGN KEY FK_CEB18CF04EC001D1');
        $this->addSql('DROP INDEX IDX_CEB18CF04EC001D1 ON subscriber_season');
        $this->addSql('ALTER TABLE subscriber_season DROP season_id');
    }
}
