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
        //end create cc_subjs_token table
    }

    public function down(Schema $schema)
    {

    }
}