<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110402164819 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //start cc_show modifications
        $show_table = $schema->getTable('cc_show');

        $show_table->addColumn('genre', 'string', array('notnull' => 0, 'length' => 255, 'default' => ""));
        //end cc_show modifications

    }

    public function down(Schema $schema)
    {
        //start cc_show modifications
        $show_table = $schema->getTable('cc_show');

        $show_table->dropColumn('genre');
        //end cc_show modifications
    }
}
