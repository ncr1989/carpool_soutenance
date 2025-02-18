<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218203538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, mdp VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FCEC9EF7BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservations (personne_id INT NOT NULL, trajet_id INT NOT NULL, INDEX IDX_4DA239A21BD112 (personne_id), INDEX IDX_4DA239D12A823 (trajet_id), PRIMARY KEY(personne_id, trajet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, personne_id INT NOT NULL, ville_id INT NOT NULL, ville_a VARCHAR(255) NOT NULL, ville_d VARCHAR(255) NOT NULL, conducteur TINYINT(1) NOT NULL, date_trajet DATE NOT NULL, nbr_places INT NOT NULL, INDEX IDX_2B5BA98CA21BD112 (personne_id), INDEX IDX_2B5BA98CA73F0036 (ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CA21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CA73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239A21BD112');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239D12A823');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CA21BD112');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CA73F0036');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE ville');
    }
}
