<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110922153933 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // move soundcloud_id from cc_show_instances to cc_files
        $this->_addSql("update cc_files as cf set soundcloud_id = csi.soundcloud_id
                        from cc_show_instances as csi
                        where csi.file_id = cf.id and file_id is not NULL");
        
        // remove soundcloud_id from cc_show_instance table
        $cc_show_instances = $schema->getTable('cc_show_instances');
        $cc_show_instances->dropColumn('soundcloud_id');
        
        // create cc_login_sttempts table
        $cc_login = $schema->createTable('cc_login_attempts');
        
        $cc_login->addColumn('ip', 'string', array('length' => 32));
        $cc_login->addColumn('attempts', 'integer', array('notnull' => 0, 'default'=> 0));
        
        $cc_login->setPrimaryKey(array('ip'));
        
        // add login_attempts column to cc_subjs table
        $cc_subjs = $schema->getTable('cc_subjs');
        $cc_subjs->addColumn('login_attempts', 'integer', array('notnull' => 0, 'default'=> 0));
    }

    public function down(Schema $schema)
    {
        
    }
}