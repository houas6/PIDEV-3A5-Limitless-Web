<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230328000921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_1');
        $this->addSql('ALTER TABLE echanges DROP FOREIGN KEY FK_PE');
        $this->addSql('ALTER TABLE echanges DROP FOREIGN KEY FK_PO');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY panier_ibfk_2');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY panier_ibfk_1');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE echanges');
        $this->addSql('DROP TABLE panier');
        $this->addSql('ALTER TABLE produit CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC276B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('DROP INDEX fk_id_user ON produit');
        $this->addSql('CREATE INDEX FK_Pu ON produit (id_user)');
        $this->addSql('ALTER TABLE utilisateur ADD numero VARCHAR(50) NOT NULL, DROP username');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id_commande INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, adresse VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, total DOUBLE PRECISION NOT NULL, INDEX id_user (id_user), PRIMARY KEY(id_commande)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE echanges (id_echange INT AUTO_INCREMENT NOT NULL, produit_echange INT NOT NULL, produit_offert INT NOT NULL, statut VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, commentaire VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX FK_PE (produit_echange), INDEX FK_PO (produit_offert), PRIMARY KEY(id_echange)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE panier (id_user INT NOT NULL, id_produit INT NOT NULL, quantite_product INT NOT NULL, INDEX id_user (id_user), INDEX id_produit (id_produit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE echanges ADD CONSTRAINT FK_PE FOREIGN KEY (produit_echange) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE echanges ADD CONSTRAINT FK_PO FOREIGN KEY (produit_offert) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT panier_ibfk_2 FOREIGN KEY (id_produit) REFERENCES produit (id_produit)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT panier_ibfk_1 FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC276B3CA4B');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC276B3CA4B');
        $this->addSql('ALTER TABLE produit CHANGE id_user id_user INT NOT NULL');
        $this->addSql('DROP INDEX fk_pu ON produit');
        $this->addSql('CREATE INDEX fk_id_user ON produit (id_user)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC276B3CA4B FOREIGN KEY (id_user) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE utilisateur ADD username VARCHAR(30) NOT NULL, DROP numero');
    }
}
