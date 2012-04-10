<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410134819 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->_addSql("UPDATE cc_files SET temp_br = bit_rate::integer");
        $this->_addSql("UPDATE cc_files SET temp_sr = sample_rate::integer");
        
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