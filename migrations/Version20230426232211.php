<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426232211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_1');
        $this->addSql('ALTER TABLE echanges DROP FOREIGN KEY FK_PO');
        $this->addSql('ALTER TABLE echanges DROP FOREIGN KEY FK_PE');
        $this->addSql('ALTER TABLE gestion des reclamations DROP FOREIGN KEY gestion des reclamations_ibfk_1');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY panier_ibfk_2');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY panier_ibfk_1');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_Pu');
        $this->addSql('ALTER TABLE reponse_reclamation DROP FOREIGN KEY reponse_reclamation_ibfk_1');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE echanges');
        $this->addSql('DROP TABLE gestion des reclamations');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE reponse_reclamation');
        $this->addSql('ALTER TABLE utilisateur ADD bloque TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id_commande INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, total DOUBLE PRECISION NOT NULL, status VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'Nonpaye\' COLLATE `utf8mb4_general_ci`, INDEX id_user (id_user), PRIMARY KEY(id_commande)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE echanges (id_echange INT AUTO_INCREMENT NOT NULL, produit_echange INT NOT NULL, produit_offert INT NOT NULL, statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, commentaire VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX FK_PE (produit_echange), INDEX FK_PO (produit_offert), PRIMARY KEY(id_echange)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE gestion des reclamations (id INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, description VARCHAR(1000) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, etat VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_client (id_client), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE panier (id_user INT NOT NULL, id_produit INT NOT NULL, quantite_product INT NOT NULL, INDEX id_produit (id_produit), INDEX id_user (id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produit (id_produit INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, nom_produit VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION NOT NULL, description VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, image BLOB NOT NULL, INDEX FK_Pu (id_user), PRIMARY KEY(id_produit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reponse_reclamation (id INT AUTO_INCREMENT NOT NULL, id_reclamation INT NOT NULL, contenu VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_rec (id_reclamation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE echanges ADD CONSTRAINT FK_PO FOREIGN KEY (produit_offert) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE echanges ADD CONSTRAINT FK_PE FOREIGN KEY (produit_echange) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE gestion des reclamations ADD CONSTRAINT gestion des reclamations_ibfk_1 FOREIGN KEY (id_client) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT panier_ibfk_2 FOREIGN KEY (id_produit) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT panier_ibfk_1 FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_Pu FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE reponse_reclamation ADD CONSTRAINT reponse_reclamation_ibfk_1 FOREIGN KEY (id_reclamation) REFERENCES gestion des reclamations (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE utilisateur DROP bloque');
    }
}
