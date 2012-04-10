<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410104441 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //add temp columns for changing bitrate and sample rate to integers.
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('temp_br', 'integer', array('notnull' => 0));
        $cc_files->addColumn('temp_sr', 'integer', array('notnull' => 0));
    }
    
    public function down(Schema $schema)
    {

    }
}