<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181005080210 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carte ADD type_id INT DEFAULT NULL, ADD image VARCHAR(255) NOT NULL, DROP type, DROP nb_carte, DROP img_carte');
        $this->addSql('ALTER TABLE carte ADD CONSTRAINT FK_BAD4FFFDC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_BAD4FFFDC54C8C93 ON carte (type_id)');
        $this->addSql('ALTER TABLE jeton ADD type_id INT DEFAULT NULL, ADD image VARCHAR(255) NOT NULL, DROP type, DROP img_jeton, CHANGE nb_jeton valeur INT NOT NULL');
        $this->addSql('ALTER TABLE jeton ADD CONSTRAINT FK_2CF647BC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_2CF647BC54C8C93 ON jeton (type_id)');
        $this->addSql('ALTER TABLE joueur ADD main LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD tas_jetons LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD score DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carte DROP FOREIGN KEY FK_BAD4FFFDC54C8C93');
        $this->addSql('ALTER TABLE jeton DROP FOREIGN KEY FK_2CF647BC54C8C93');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP INDEX IDX_BAD4FFFDC54C8C93 ON carte');
        $this->addSql('ALTER TABLE carte ADD nb_carte INT NOT NULL, ADD img_carte VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP type_id, CHANGE image type VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('DROP INDEX IDX_2CF647BC54C8C93 ON jeton');
        $this->addSql('ALTER TABLE jeton ADD img_jeton VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP type_id, CHANGE image type VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE valeur nb_jeton INT NOT NULL');
        $this->addSql('ALTER TABLE joueur DROP main, DROP tas_jetons, DROP score');
    }
}
