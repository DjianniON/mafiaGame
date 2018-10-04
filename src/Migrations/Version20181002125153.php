<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181002125153 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE carte (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, nb_carte INT NOT NULL, img_carte VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jeton (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, nb_jeton INT NOT NULL, img_jeton VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, joueur1 LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', joueur2 LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', terrain LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', deck LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', defausse TINYINT(1) NOT NULL, tas_jeton LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', historique_partie LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', main_joueur TINYINT(1) NOT NULL, status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD nb_victoires INT NOT NULL, ADD elo INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE carte');
        $this->addSql('DROP TABLE jeton');
        $this->addSql('DROP TABLE partie');
        $this->addSql('ALTER TABLE user DROP nb_victoires, DROP elo');
    }
}
