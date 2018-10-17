<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181016063030 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE joueur_partie');
        $this->addSql('ALTER TABLE partie ADD joueur1_id INT NOT NULL, ADD joueur2_id INT NOT NULL, DROP joueur1, DROP joueur2');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D92C1E237 FOREIGN KEY (joueur1_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D80744DD9 FOREIGN KEY (joueur2_id) REFERENCES joueur (id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D92C1E237 ON partie (joueur1_id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D80744DD9 ON partie (joueur2_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE joueur_partie (joueur_id INT NOT NULL, partie_id INT NOT NULL, INDEX IDX_EC200FB1A9E2D76C (joueur_id), INDEX IDX_EC200FB1E075F7A4 (partie_id), PRIMARY KEY(joueur_id, partie_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE joueur_partie ADD CONSTRAINT FK_EC200FB1A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueur_partie ADD CONSTRAINT FK_EC200FB1E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D92C1E237');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D80744DD9');
        $this->addSql('DROP INDEX IDX_59B1F3D92C1E237 ON partie');
        $this->addSql('DROP INDEX IDX_59B1F3D80744DD9 ON partie');
        $this->addSql('ALTER TABLE partie ADD joueur1 LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', ADD joueur2 LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', DROP joueur1_id, DROP joueur2_id');
    }
}
