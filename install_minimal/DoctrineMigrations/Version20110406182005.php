<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110406182005 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //start cc_show_instances modifications
        $cc_show_instances = $schema->getTable('cc_show_instances');

        $cc_show_instances->addColumn('time_filled', 'time',  array('notnull' => false));
        //end cc_show_instances modifications

        //start cc_show_rebroadcast modifications
        $cc_show_rebroadcast = $schema->getTable('cc_show_rebroadcast');

        $type = $cc_show_rebroadcast->getColumn('start_time')->getType()->getName();
        if($type == 'datetime') {
            $cc_show_rebroadcast->dropColumn('start_time');
            $cc_show_rebroadcast->addColumn('start_time', 'time',  array('notnull' => true));
        }    
        //end cc_show_rebroadcast modifications
    }

    public function down(Schema $schema)
    {
        //start cc_show_instances modifications
        $cc_show_instances = $schema->getTable('cc_show_instances');

        $cc_show_instances->dropColumn('time_filled');
        //end cc_show_instances modifications

        //start cc_show_rebroadcast modifications
        $cc_show_rebroadcast = $schema->getTable('cc_show_rebroadcast');

        $type = $cc_show_rebroadcast->getColumn('start_time')->getType()->getName();
        if($type == 'datetime') {
            $cc_show_rebroadcast->dropColumn('start_time');
            $cc_show_rebroadcast->addColumn('start_time', 'datetime',  array('notnull' => 1));
        }  
        //end cc_show_rebroadcast modifications
    }
}
