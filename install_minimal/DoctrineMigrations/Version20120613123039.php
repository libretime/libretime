<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120613123039 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->getTable("cc_subjs");
        $table->addColumn("cell_phone", "string");
    }

    public function down(Schema $schema)
    {

    }
}
