<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260729194115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(120) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(120) NOT NULL, adresse VARCHAR(180) NOT NULL, active TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(120) NOT NULL, telephone VARCHAR(30) NOT NULL, adresse VARCHAR(180) NOT NULL, ville VARCHAR(80) NOT NULL, code_postal VARCHAR(10) NOT NULL, agence_reference_id INT NOT NULL, INDEX IDX_C7440455E65CE5CD (agence_reference_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE colis (id INT AUTO_INCREMENT NOT NULL, numero_suivi VARCHAR(50) NOT NULL, statut VARCHAR(30) NOT NULL, destinataire VARCHAR(120) NOT NULL, adresse_livraison VARCHAR(180) NOT NULL, ville_livraison VARCHAR(80) NOT NULL, code_postal_livraison VARCHAR(10) NOT NULL, poids_kg DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, client_id INT NOT NULL, agence_depot_id INT NOT NULL, UNIQUE INDEX UNIQ_470BDFF981E3E1B2 (numero_suivi), INDEX IDX_470BDFF919EB6921 (client_id), INDEX IDX_470BDFF9A498D443 (agence_depot_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mouvement_colis (id INT AUTO_INCREMENT NOT NULL, statut VARCHAR(30) NOT NULL, lieu VARCHAR(160) NOT NULL, commentaire LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, colis_id INT NOT NULL, INDEX IDX_6DDF41724D268D70 (colis_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(120) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455E65CE5CD FOREIGN KEY (agence_reference_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF9A498D443 FOREIGN KEY (agence_depot_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE mouvement_colis ADD CONSTRAINT FK_6DDF41724D268D70 FOREIGN KEY (colis_id) REFERENCES colis (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455E65CE5CD');
        $this->addSql('ALTER TABLE colis DROP FOREIGN KEY FK_470BDFF919EB6921');
        $this->addSql('ALTER TABLE colis DROP FOREIGN KEY FK_470BDFF9A498D443');
        $this->addSql('ALTER TABLE mouvement_colis DROP FOREIGN KEY FK_6DDF41724D268D70');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE colis');
        $this->addSql('DROP TABLE mouvement_colis');
        $this->addSql('DROP TABLE user');
    }
}
