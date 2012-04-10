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
        $cc_subjs_token->addColumn('user_id', 'integer',  array('notnull' => 1));
        $cc_subjs_token->addColumn('action', 'string', array('length' => 255, 'notnull' => 1));
        $cc_subjs_token->addColumn('token', 'string', array('length' => 40, 'notnull' => 1));
        $cc_subjs_token->addColumn('created', 'datetime',  array('notnull' => 1));
        
        $cc_subjs_token->setPrimaryKey(array('id'));
        
        $cc_subjs = $schema->getTable('cc_subjs');
        $cc_subjs_token->addNamedForeignKeyConstraint('cc_subjs_token_userid_fkey', $cc_subjs, array('user_id'), array('id'));
        $cc_subjs_token->addUniqueIndex(array('token'), 'uniq_token');
        //end create cc_subjs_token table
    }

    public function down(Schema $schema)
    {

    }
}