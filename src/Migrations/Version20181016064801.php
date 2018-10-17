<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181016064801 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE partie_joueur (partie_id INT NOT NULL, joueur_id INT NOT NULL, INDEX IDX_AE7EDCA9E075F7A4 (partie_id), INDEX IDX_AE7EDCA9A9E2D76C (joueur_id), PRIMARY KEY(partie_id, joueur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA9E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA9A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D80744DD9');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D92C1E237');
        $this->addSql('DROP INDEX IDX_59B1F3D92C1E237 ON partie');
        $this->addSql('DROP INDEX IDX_59B1F3D80744DD9 ON partie');
        $this->addSql('ALTER TABLE partie DROP joueur1_id, DROP joueur2_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE partie_joueur');
        $this->addSql('ALTER TABLE partie ADD joueur1_id INT NOT NULL, ADD joueur2_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D80744DD9 FOREIGN KEY (joueur2_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D92C1E237 FOREIGN KEY (joueur1_id) REFERENCES joueur (id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D92C1E237 ON partie (joueur1_id)');
        $this->addSql('CREATE INDEX IDX_59B1F3D80744DD9 ON partie (joueur2_id)');
    }
}
