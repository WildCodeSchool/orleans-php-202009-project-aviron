<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201209155252 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT NOT NULL, season_id INT NOT NULL, subscription_date DATE NOT NULL, INDEX IDX_A3C664D37808B1AD (subscriber_id), INDEX IDX_A3C664D34EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D37808B1AD FOREIGN KEY (subscriber_id) REFERENCES subscriber (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D34EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('DROP TABLE subscriber_season');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscriber_season (id INT AUTO_INCREMENT NOT NULL, subscriber_id INT NOT NULL, season_id INT NOT NULL, subscription_date DATE NOT NULL, INDEX IDX_CEB18CF04EC001D1 (season_id), INDEX IDX_CEB18CF07808B1AD (subscriber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE subscriber_season ADD CONSTRAINT FK_CEB18CF04EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subscriber_season ADD CONSTRAINT FK_CEB18CF07808B1AD FOREIGN KEY (subscriber_id) REFERENCES subscriber (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE subscription');
    }
}
