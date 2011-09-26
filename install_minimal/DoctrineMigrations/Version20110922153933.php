<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110922153933 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // add soundcloud_id, soundcloud_error_code, soundcloud_error_msg columns to cc_files
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('soundcloud_id', 'integer', array('notnull' => 0, 'default'=> NULL));
        $cc_files->addColumn('soundcloud_error_code', 'integer', array('notnull' => 0, 'default'=> NULL));
        $cc_files->addColumn('soundcloud_error_msg', 'string', array('length' => 255, 'notnull' => 0, 'default'=> NULL));
    }

    public function down(Schema $schema)
    {
        
    }
}