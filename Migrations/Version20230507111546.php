<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507111546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_1');
        $this->addSql('ALTER TABLE echanges DROP FOREIGN KEY FK_PO');
        $this->addSql('ALTER TABLE echanges DROP FOREIGN KEY FK_PE');
        $this->addSql('ALTER TABLE gestion_des_reclamations DROP FOREIGN KEY gestion_des_reclamations_ibfk_1');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_Pu');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE echanges');
        $this->addSql('DROP TABLE gestion_des_reclamations');
        $this->addSql('DROP TABLE produit');
        $this->addSql('ALTER TABLE reclamations DROP FOREIGN KEY FK_1CAD6B76C895D8ED');
        $this->addSql('DROP INDEX IDX_1CAD6B76C895D8ED ON reclamations');
        $this->addSql('ALTER TABLE reclamations DROP reponse_reclamation_id');
        $this->addSql('ALTER TABLE reponse_reclamation ADD reclamation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse_reclamation ADD CONSTRAINT FK_C7CB51012D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamations (id)');
        $this->addSql('CREATE INDEX IDX_1CAD6B76C895D8ED ON reponse_reclamation (reclamation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id_commande INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, total DOUBLE PRECISION NOT NULL, status VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'Nonpaye\' COLLATE `utf8mb4_general_ci`, INDEX id_user (id_user), PRIMARY KEY(id_commande)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE echanges (id_echange INT AUTO_INCREMENT NOT NULL, produit_echange INT NOT NULL, produit_offert INT NOT NULL, statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, commentaire VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX FK_PO (produit_offert), INDEX FK_PE (produit_echange), PRIMARY KEY(id_echange)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE gestion_des_reclamations (id INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, description VARCHAR(1000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, etat VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_client (id_client), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produit (id_produit INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, nom_produit VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION NOT NULL, description VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, image BLOB NOT NULL, INDEX FK_Pu (id_user), PRIMARY KEY(id_produit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE echanges ADD CONSTRAINT FK_PO FOREIGN KEY (produit_offert) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE echanges ADD CONSTRAINT FK_PE FOREIGN KEY (produit_echange) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE gestion_des_reclamations ADD CONSTRAINT gestion_des_reclamations_ibfk_1 FOREIGN KEY (id_client) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_Pu FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE reclamations ADD reponse_reclamation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamations ADD CONSTRAINT FK_1CAD6B76C895D8ED FOREIGN KEY (reponse_reclamation_id) REFERENCES reponse_reclamation (id)');
        $this->addSql('CREATE INDEX IDX_1CAD6B76C895D8ED ON reclamations (reponse_reclamation_id)');
        $this->addSql('ALTER TABLE reponse_reclamation DROP FOREIGN KEY FK_C7CB51012D6BA2D9');
        $this->addSql('DROP INDEX IDX_1CAD6B76C895D8ED ON reponse_reclamation');
        $this->addSql('ALTER TABLE reponse_reclamation DROP reclamation_id');
    }
}
