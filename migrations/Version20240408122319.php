<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408122319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE products MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON products');
        // $this->addSql('ALTER TABLE products DROP id');
        $this->addSql('ALTER TABLE products ADD PRIMARY KEY (p_ref)');
        $this->addSql('ALTER TABLE comments CHANGE p_ref p_ref VARCHAR(10) DEFAULT NULL');
        // added this so p_ref can not be null (this was removed automaticly in the next migration)
        $this->addSql("ALTER TABLE comments MODIFY p_ref VARCHAR(10) NOT NULL");
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A47B0EF97 FOREIGN KEY (p_ref) REFERENCES products (p_ref)');
        $this->addSql('CREATE INDEX IDX_5F9E962A47B0EF97 ON comments (p_ref)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A47B0EF97');
        $this->addSql('DROP INDEX IDX_5F9E962A47B0EF97 ON comments');
        $this->addSql('ALTER TABLE comments CHANGE p_ref p_ref VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE products ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
