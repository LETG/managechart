<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405075155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE attribut_spatial_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE chart_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE data_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE data_source_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE flag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE series_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "users_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE y_axis_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE attribut_spatial (id INT NOT NULL, data_list_id INT NOT NULL, nameAttribut VARCHAR(255) NOT NULL, valueAttribut VARCHAR(255) NOT NULL, keywordAttribut VARCHAR(255) NOT NULL, typeAttribut VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BBFA706959A5407A ON attribut_spatial (data_list_id)');
        $this->addSql('CREATE TABLE chart (id INT NOT NULL, nameChart VARCHAR(255) NOT NULL, titleChart VARCHAR(255) DEFAULT NULL, subtitleChart VARCHAR(255) DEFAULT NULL, legendChart BOOLEAN NOT NULL, tooltipChart BOOLEAN DEFAULT \'true\' NOT NULL, creditsChart TEXT NOT NULL, urlcreditsChart TEXT DEFAULT NULL, typeChart TEXT NOT NULL, xAxisTitle VARCHAR(255) DEFAULT NULL, xAxisUnit VARCHAR(255) DEFAULT NULL, xAxisType VARCHAR(255) NOT NULL, invertedChart BOOLEAN DEFAULT NULL, gapSizeChart INT NOT NULL, polarType VARCHAR(255) DEFAULT NULL, pieType VARCHAR(255) DEFAULT NULL, typestacked VARCHAR(255) DEFAULT NULL, exportPrintChart BOOLEAN NOT NULL, exportCSVChart BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE data_list (id INT NOT NULL, data_source_id INT NOT NULL, nameData VARCHAR(255) NOT NULL, dateData TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, requestData TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF8733FA1A935C57 ON data_list (data_source_id)');
        $this->addSql('CREATE TABLE data_source (id INT NOT NULL, nameBDD VARCHAR(255) NOT NULL, descriptionBDD TEXT NOT NULL, hostBDD VARCHAR(255) NOT NULL, portBDD INT NOT NULL, loginBDD VARCHAR(1024) NOT NULL, passwordBDD VARCHAR(1024) NOT NULL, typeBDD INT NOT NULL, typeStrBDD VARCHAR(255) NOT NULL, dateBDD TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nameCon VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE flag (id INT NOT NULL, data_list_id INT NOT NULL, y_axis_id INT NOT NULL, parameterDataList VARCHAR(255) DEFAULT NULL, onseries VARCHAR(255) DEFAULT NULL, titleFlag VARCHAR(255) DEFAULT NULL, shapeflag VARCHAR(255) NOT NULL, colorflag VARCHAR(255) NOT NULL, widthflag VARCHAR(255) DEFAULT NULL, styleflag VARCHAR(255) DEFAULT NULL, yaxisOrder INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D1F4EB9A59A5407A ON flag (data_list_id)');
        $this->addSql('CREATE INDEX IDX_D1F4EB9A88174EDB ON flag (y_axis_id)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE series (id INT NOT NULL, y_axis_id INT NOT NULL, data_list_id INT NOT NULL, titleSerie VARCHAR(255) DEFAULT NULL, unitSerie VARCHAR(255) DEFAULT NULL, typeSerie VARCHAR(255) NOT NULL, colorSerie VARCHAR(255) DEFAULT NULL, markerSerie BOOLEAN NOT NULL, dashStyleSerie VARCHAR(255) NOT NULL, parameterDataList VARCHAR(255) DEFAULT NULL, yaxisOrder INT DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, innersize VARCHAR(255) DEFAULT NULL, colsize DOUBLE PRECISION DEFAULT \'1\', PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3A10012D88174EDB ON series (y_axis_id)');
        $this->addSql('CREATE INDEX IDX_3A10012D59A5407A ON series (data_list_id)');
        $this->addSql('CREATE TABLE "users" (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON "users" (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('CREATE TABLE y_axis (id INT NOT NULL, chart_id INT NOT NULL, titleYAxis VARCHAR(255) DEFAULT NULL, typeYAxis VARCHAR(255) NOT NULL, top VARCHAR(255) DEFAULT NULL, height VARCHAR(255) DEFAULT NULL, opposite BOOLEAN DEFAULT \'false\' NOT NULL, orderY INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_83E7FC56BEF83E0A ON y_axis (chart_id)');
        $this->addSql('ALTER TABLE attribut_spatial ADD CONSTRAINT FK_BBFA706959A5407A FOREIGN KEY (data_list_id) REFERENCES data_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data_list ADD CONSTRAINT FK_FF8733FA1A935C57 FOREIGN KEY (data_source_id) REFERENCES data_source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flag ADD CONSTRAINT FK_D1F4EB9A59A5407A FOREIGN KEY (data_list_id) REFERENCES data_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flag ADD CONSTRAINT FK_D1F4EB9A88174EDB FOREIGN KEY (y_axis_id) REFERENCES y_axis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE series ADD CONSTRAINT FK_3A10012D88174EDB FOREIGN KEY (y_axis_id) REFERENCES y_axis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE series ADD CONSTRAINT FK_3A10012D59A5407A FOREIGN KEY (data_list_id) REFERENCES data_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE y_axis ADD CONSTRAINT FK_83E7FC56BEF83E0A FOREIGN KEY (chart_id) REFERENCES chart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE y_axis DROP CONSTRAINT FK_83E7FC56BEF83E0A');
        $this->addSql('ALTER TABLE attribut_spatial DROP CONSTRAINT FK_BBFA706959A5407A');
        $this->addSql('ALTER TABLE flag DROP CONSTRAINT FK_D1F4EB9A59A5407A');
        $this->addSql('ALTER TABLE series DROP CONSTRAINT FK_3A10012D59A5407A');
        $this->addSql('ALTER TABLE data_list DROP CONSTRAINT FK_FF8733FA1A935C57');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE flag DROP CONSTRAINT FK_D1F4EB9A88174EDB');
        $this->addSql('ALTER TABLE series DROP CONSTRAINT FK_3A10012D88174EDB');
        $this->addSql('DROP SEQUENCE attribut_spatial_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE chart_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE data_list_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE data_source_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE flag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE series_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "users_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE y_axis_id_seq CASCADE');
        $this->addSql('DROP TABLE attribut_spatial');
        $this->addSql('DROP TABLE chart');
        $this->addSql('DROP TABLE data_list');
        $this->addSql('DROP TABLE data_source');
        $this->addSql('DROP TABLE flag');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE series');
        $this->addSql('DROP TABLE "users"');
        $this->addSql('DROP TABLE y_axis');
    }
}
