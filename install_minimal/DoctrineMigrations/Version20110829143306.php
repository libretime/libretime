<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110829143306 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //create cc_stream_setting table
        $cc_stream_setting = $schema->createTable('cc_stream_setting');
        
        $cc_stream_setting->addColumn('keyname', 'string', array('length' => 64));
        $cc_stream_setting->addColumn('value', 'string', array('length' => 255));
        $cc_stream_setting->addColumn('type', 'string', array('length' => 16));
        
        $cc_stream_setting->setPrimaryKey(array('keyname'));
        //end create cc_stream_setting table
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('cc_stream_setting');
    }
}