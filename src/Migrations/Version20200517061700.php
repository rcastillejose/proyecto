<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200517061700 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, empleado_id INT NOT NULL, grupo_id INT NOT NULL, estado_id INT NOT NULL, fecha DATETIME NOT NULL, asunto VARCHAR(255) NOT NULL, mensaje LONGTEXT NOT NULL, respuesta LONGTEXT DEFAULT NULL, INDEX IDX_97A0ADA3DE734E51 (cliente_id), INDEX IDX_97A0ADA3952BE730 (empleado_id), INDEX IDX_97A0ADA39C833003 (grupo_id), INDEX IDX_97A0ADA39F5A440B (estado_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3DE734E51 FOREIGN KEY (cliente_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3952BE730 FOREIGN KEY (empleado_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA39C833003 FOREIGN KEY (grupo_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA39F5A440B FOREIGN KEY (estado_id) REFERENCES estado (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ticket');
    }
}
