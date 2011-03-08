<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110308003959 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $schema->getTable("cc_show_instances");
        $table->addColumn("record", "boolean", array( 'notnull' => 0, 'default' => 0));
    }

    public function down(Schema $schema)
    {
        $table = $schema->getTable("cc_show_instances");
        $table->dropColumn("record");
    }
}
