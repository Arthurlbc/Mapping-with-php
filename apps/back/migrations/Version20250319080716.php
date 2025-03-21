<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250319080716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table courses and members.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
        CREATE TABLE courses (
            id VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description VARCHAR(255) NOT NULL,
            duration INT NOT NULL,
            author VARCHAR(255) DEFAULT NULL,
            member_ids LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)',
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
    SQL
        );

        $this->addSql(<<<SQL
        CREATE TABLE `members` (
            id VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            courses_complete LONGTEXT DEFAULT NULL COMMENT '(DC2Type:simple_array)',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE `members`');
    }
}
