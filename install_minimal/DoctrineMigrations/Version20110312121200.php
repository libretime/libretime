<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110312121200 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $schema->dropTable("cc_backup");
        $schema->dropTable("cc_trans");
    }

    public function down(Schema $schema)
    {
    }
}
