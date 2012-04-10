<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410104441 extends AbstractMigration
{
    public function preUp(Schema $schema) 
    {
        //start changes to cc_files 
        //add temp columns for changing bitrate and sample rate to integers.
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('temp_br', 'integer',  array('notnull' => 0));
        $cc_files->addColumn('temp_sr', 'integer', array('notnull' => 0));
        //end changes to cc_files
    }
        
    public function up(Schema $schema)
    {
        $this->_addSql("UPDATE cc_files SET temp_br = bit_rate::integer");
        $this->_addSql("UPDATE cc_files SET temp_sr = sample_rate::integer");
    }
    
    public function postUp(Schema $schema)
    {
        $cc_files = $schema->getTable('cc_files');
        $cc_files->dropColumn('bit_rate');
        $cc_files->dropColumn('sample_rate');
        
        $cc_files->renameColumn('temp_br', 'bit_rate');
        $cc_files->renameColumn('temp_sr', 'sample_rate');
    }

    public function down(Schema $schema)
    {

    }
}