<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200517003523 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, grupo_id INT NOT NULL, estado_id INT NOT NULL, fecha DATETIME NOT NULL, pregunta VARCHAR(255) NOT NULL, respuesta LONGTEXT NOT NULL, INDEX IDX_E8FF75CCA76ED395 (user_id), INDEX IDX_E8FF75CC9C833003 (grupo_id), INDEX IDX_E8FF75CC9F5A440B (estado_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE estado (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CC9C833003 FOREIGN KEY (grupo_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE faq ADD CONSTRAINT FK_E8FF75CC9F5A440B FOREIGN KEY (estado_id) REFERENCES estado (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE faq DROP FOREIGN KEY FK_E8FF75CC9F5A440B');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE estado');
    }
}
