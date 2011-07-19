<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110713161043 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //create cc_country table
        $cc_country = $schema->createTable('cc_country');
        
        $cc_country->addColumn('isocode', 'string', array('length' => 3));
        $cc_country->addColumn('name', 'string', array('length' => 255));
        
        $cc_country->setPrimaryKey(array('isocode'));
        //end create cc_country table
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('cc_country');
    }
}