<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210208133652 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Fill library tables';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO `category`(`name`, `label`, `new_group`, `old_group`, `color`) VALUES ("Jeune 9 ans","J9","Jeunes","Benjamin","#37cf9baa"),("Jeune 10 ans","J10","Jeunes","Benjamin","#37cf9baa"),("Jeune 11 ans","J11", "Jeunes","Benjamin","#37cf9baa"),("Jeune 12 ans","J12","Jeunes","Benjamin","#37cf9baa"),("Jeune 13 ans","J13","Jeunes","Minime","#f2e350aa"),("Jeune 14 ans","J14","Jeunes","Minime","#f2e350aa"),("Junior 15 ans","J15","Juniors","Cadet","#e69138ff"),("Junior 16 ans","J16","Juniors","Cadet", "#e69138ff"),("Junior 17 ans","J17","Juniors","Junior","#d56741aa"),("Junior 18 ans","J18","Juniors","Junior","#d56741aa"),("Senior, moins de 23 ans","S-23","Seniors","Senior B","#a65bd7aa"),("Senior, 23 et +","S","Seniors","Senior A","#6688c3aa")');
        $this->addSql('INSERT INTO `licence`(`name`, `acronym`, `color`) VALUES ("Compétition","A","#6688c3aa"),("Universitaire","U","#a65bd7aa"),("Découverte","D7","#37cf9baa"),("Découverte","D30","#37cf9baa"),("Découverte","D90","#37cf9baa"),("Indoor","I","#f2e350aa")');
        $this->addSql('INSERT INTO `status`(`name`, `label`, `color`) VALUES ("Transfert","T","#37cf9baa"),("Nouveau","N","#f2e350aa"),("Renouvellement","R","#e69138ff"),("Reprise","P","#d56741aa")');
    }
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE TABLE `category`');
        $this->addSql('TRUNCATE TABLE `licence`');
        $this->addSql('TRUNCATE TABLE `status`');
    }
}
