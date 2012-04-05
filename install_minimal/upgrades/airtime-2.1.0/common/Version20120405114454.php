<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120405114454 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //create cc_subjs_token table
        $cc_subjs_token = $schema->createTable('cc_subjs_token');
        
        $cc_subjs_token->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $cc_subjs_token->addColumn('show_id', 'integer',  array('notnull' => 1));
        $cc_subjs_token->addColumn('action', 'string', array('length' => 255, 'notnull' => 1));
        $cc_subjs_token->addColumn('token', 'string', array('length' => 40, 'notnull' => 1));
        $cc_subjs_token->addColumn('created', 'datetime',  array('notnull' => 1));
        
        $cc_subjs_token->setPrimaryKey(array('id'));
        
        $cc_subjs = $schema->getTable('cc_subjs');
        $cc_subjs_token->addNamedForeignKeyConstraint('cc_subjs_token_userid_fkey', $cc_subjs, array('user_id'), array('id'));
        $cc_subjs_token->addUniqueIndex(array('token'), 'uniq_token');
        //end create cc_subjs_token table
        
        // change 'soundcloud_upload' -> 'soundcloud_auto_upload_recorded_show' CC-2928
        //$this->_addSql("UPDATE cc_pref SET keystr = 'soundcloud_auto_upload_recorded_show'
                //WHERE keystr = 'soundcloud_upload'");

        //start changes to cc_files
        //$cc_files = $schema->getTable('cc_files');
        
        $con = Doctrine_Manager::getInstance()->connection();
        $con->execute("ALTER TABLE cc_files ALTER COLUMN bit_rate TYPE integer");
        $con->execute("ALTER TABLE cc_files ALTER COLUMN sample_rate TYPE integer");
        
        //end changes to cc_files
    }

    public function down(Schema $schema)
    {

    }
}