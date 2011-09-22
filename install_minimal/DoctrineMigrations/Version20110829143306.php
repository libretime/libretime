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
        
        // add soundcloud_id, soundcloud_error_code, soundcloud_error_msg columns to cc_files
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('soundcloud_id', 'integer', array('notnull' => 0, 'default'=> NULL));
        $cc_files->addColumn('soundcloud_error_code', 'integer', array('notnull' => 0, 'default'=> NULL));
        $cc_files->addColumn('soundcloud_error_msg', 'string', array('length' => 255, 'notnull' => 0, 'default'=> NULL));
    }
    
    public function postUp(){
        // move soundcloud_id from cc_show_instances to cc_files
        $this->_addSql("update cc_files as cf set soundcloud_id = csi.soundcloud_id
                        from cc_show_instances as csi
                        where csi.file_id = cf.id and file_id is not NULL");
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('cc_stream_setting');
    }
}