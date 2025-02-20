<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220090250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CA73F0036');
        $this->addSql('DROP INDEX IDX_2B5BA98CA73F0036 ON trajet');
        $this->addSql('ALTER TABLE trajet ADD ville_depart_id INT NOT NULL, DROP ville_a, DROP ville_d, CHANGE ville_id ville_arrivee_id INT NOT NULL');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C34AC9A4B FOREIGN KEY (ville_arrivee_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C497832A6 FOREIGN KEY (ville_depart_id) REFERENCES ville (id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C34AC9A4B ON trajet (ville_arrivee_id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C497832A6 ON trajet (ville_depart_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C34AC9A4B');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C497832A6');
        $this->addSql('DROP INDEX IDX_2B5BA98C34AC9A4B ON trajet');
        $this->addSql('DROP INDEX IDX_2B5BA98C497832A6 ON trajet');
        $this->addSql('ALTER TABLE trajet ADD ville_id INT NOT NULL, ADD ville_a VARCHAR(255) NOT NULL, ADD ville_d VARCHAR(255) NOT NULL, DROP ville_arrivee_id, DROP ville_depart_id');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CA73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2B5BA98CA73F0036 ON trajet (ville_id)');
    }
}
