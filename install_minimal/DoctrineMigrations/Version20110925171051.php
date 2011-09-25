<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110925171051 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // move soundcloud_id from cc_show_instances to cc_files
        $this->_addSql("update cc_files as cf set soundcloud_id = csi.soundcloud_id
                                from cc_show_instances as csi
                                where csi.file_id = cf.id and file_id is not NULL");
        
        // remove soundcloud_id from cc_show_instance table
        $cc_show_instances = $schema->getTable('cc_show_instances');
        $cc_show_instances->dropColumn('soundcloud_id');
    }

    public function down(Schema $schema)
    {

    }
}