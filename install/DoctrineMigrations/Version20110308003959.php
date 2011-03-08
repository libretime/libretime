<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110308003959 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $table = $this->getTable("cc_show_instances");
        $table->addColumn("record", "boolean");
    }

    public function down(Schema $schema)
    {
        $table = $this->getTable("cc_show_instances");
        $table->dropColumn("record");
    }
}
