<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Face ID columns to utilisateurs table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs ADD face_id_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD face_id_enrolled TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE utilisateurs ADD face_id_enrollment_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs DROP face_id_token');
        $this->addSql('ALTER TABLE utilisateurs DROP face_id_enrolled');
        $this->addSql('ALTER TABLE utilisateurs DROP face_id_enrollment_date');
    }
}
