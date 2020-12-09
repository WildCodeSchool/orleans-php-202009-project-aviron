<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201209063348 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE season_subscriber (season_id INT NOT NULL, subscriber_id INT NOT NULL, INDEX IDX_9D3E96CE4EC001D1 (season_id), INDEX IDX_9D3E96CE7808B1AD (subscriber_id), PRIMARY KEY(season_id, subscriber_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE season_subscriber ADD CONSTRAINT FK_9D3E96CE4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE season_subscriber ADD CONSTRAINT FK_9D3E96CE7808B1AD FOREIGN KEY (subscriber_id) REFERENCES subscriber (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE season_subscriber');
    }
}
