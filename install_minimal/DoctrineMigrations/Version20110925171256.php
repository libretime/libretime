<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110925171256 extends AbstractMigration
{
    public function up(Schema $schema)
    {
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