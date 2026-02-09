<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260209120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add teacher_id foreign key to course table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE course ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB941807E1D FOREIGN KEY (teacher_id) REFERENCES utilisateurs (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_169E6FB941807E1D ON course (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB941807E1D');
        $this->addSql('DROP INDEX IDX_169E6FB941807E1D ON course');
        $this->addSql('ALTER TABLE course DROP teacher_id');
    }
}
