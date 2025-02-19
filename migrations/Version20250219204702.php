<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219204702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98C25165746');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CDDDF1A2');
        $this->addSql('DROP INDEX IDX_2B5BA98CDDDF1A2 ON trajet');
        $this->addSql('DROP INDEX IDX_2B5BA98C25165746 ON trajet');
        $this->addSql('ALTER TABLE trajet ADD ville_id INT NOT NULL, ADD ville_a VARCHAR(255) NOT NULL, ADD ville_d VARCHAR(255) NOT NULL, DROP ville_depart, DROP ville_arrive');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CA73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('CREATE INDEX IDX_2B5BA98CA73F0036 ON trajet (ville_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CA73F0036');
        $this->addSql('DROP INDEX IDX_2B5BA98CA73F0036 ON trajet');
        $this->addSql('ALTER TABLE trajet ADD ville_arrive INT NOT NULL, DROP ville_a, DROP ville_d, CHANGE ville_id ville_depart INT NOT NULL');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98C25165746 FOREIGN KEY (ville_arrive) REFERENCES ville (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CDDDF1A2 FOREIGN KEY (ville_depart) REFERENCES ville (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2B5BA98CDDDF1A2 ON trajet (ville_depart)');
        $this->addSql('CREATE INDEX IDX_2B5BA98C25165746 ON trajet (ville_arrive)');
    }
}
