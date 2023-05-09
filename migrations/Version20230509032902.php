<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509032902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(' ALTER TABLE `utilisateur` CHANGE `reset_token` `reset_token` VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(' ALTER TABLE `utilisateur` CHANGE `reset_token` `reset_token` VARCHAR(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL');

    }
}
