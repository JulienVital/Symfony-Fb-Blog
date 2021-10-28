<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211028095716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project ADD image_showcase_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE76925763 FOREIGN KEY (image_showcase_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE76925763 ON project (image_showcase_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image CHANGE url url VARCHAR(400) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE76925763');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE76925763 ON project');
        $this->addSql('ALTER TABLE project DROP image_showcase_id');
    }
}
