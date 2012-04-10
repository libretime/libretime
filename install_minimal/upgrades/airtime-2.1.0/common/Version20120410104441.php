<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410104441 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //start changes to cc_files
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('temp_br', 'integer');
        $cc_files->addColumn('temp_sr', 'integer');
        
        $this->_addSql("UPDATE cc_files SET temp_br = bit_rate::integer");
        $this->_addSql("UPDATE cc_files SET temp_sr = sample_rate::integer");
        
        //end changes to cc_files
    }

    public function down(Schema $schema)
    {

    }
}