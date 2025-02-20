<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220175432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ville ADD insee_code VARCHAR(10) NOT NULL, ADD city_code VARCHAR(255) NOT NULL, ADD zip_code VARCHAR(10) NOT NULL, ADD label VARCHAR(255) NOT NULL, ADD latitude NUMERIC(10, 6) DEFAULT NULL, ADD longitude NUMERIC(10, 6) DEFAULT NULL, ADD department_name VARCHAR(100) NOT NULL, ADD department_number VARCHAR(10) NOT NULL, ADD region_name VARCHAR(100) NOT NULL, ADD region_geojson_name VARCHAR(100) NOT NULL, DROP nom, DROP code_postal');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_43C3D9C315A3C1BC ON ville (insee_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_43C3D9C315A3C1BC ON ville');
        $this->addSql('ALTER TABLE ville ADD nom VARCHAR(255) NOT NULL, ADD code_postal VARCHAR(255) NOT NULL, DROP insee_code, DROP city_code, DROP zip_code, DROP label, DROP latitude, DROP longitude, DROP department_name, DROP department_number, DROP region_name, DROP region_geojson_name');
    }
}
