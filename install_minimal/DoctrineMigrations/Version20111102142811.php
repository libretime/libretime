<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20111102142811 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // change 'soundcloud_upload' -> 'soundcloud_auto_upload_recorded_show' CC-2928
        $this->_addSql("UPDATE cc_pref SET keystr = 'soundcloud_auto_upload_recorded_show'
                        WHERE keystr = 'soundcloud_upload'");
        
        // add soundcloud_link_to_file
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('soundcloud_link_to_file', 'string', array('length' => 4096, 'notnull' => 0, 'default'=> NULL));
    }

    public function down(Schema $schema)
    {

    }
}