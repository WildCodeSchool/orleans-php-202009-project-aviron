<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201210090525 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription ADD licence_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D326EF07C9 FOREIGN KEY (licence_id) REFERENCES licence (id)');
        $this->addSql('CREATE INDEX IDX_A3C664D326EF07C9 ON subscription (licence_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D326EF07C9');
        $this->addSql('DROP INDEX IDX_A3C664D326EF07C9 ON subscription');
        $this->addSql('ALTER TABLE subscription DROP licence_id');
    }
}
