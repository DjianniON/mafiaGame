<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181004135008 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, INDEX IDX_FD71A9C567B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueur_partie (joueur_id INT NOT NULL, partie_id INT NOT NULL, INDEX IDX_EC200FB1A9E2D76C (joueur_id), INDEX IDX_EC200FB1E075F7A4 (partie_id), PRIMARY KEY(joueur_id, partie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C567B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE joueur_partie ADD CONSTRAINT FK_EC200FB1A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueur_partie ADD CONSTRAINT FK_EC200FB1E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE joueur_partie DROP FOREIGN KEY FK_EC200FB1A9E2D76C');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE joueur_partie');
    }
}
