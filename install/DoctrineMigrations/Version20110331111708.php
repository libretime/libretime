<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema,
    Doctrine\DBAL\Schema\Table,
    Doctrine\DBAL\Schema\Column,
    Doctrine\DBAL\Types\Type;

class Version20110331111708 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //start cc_show_instances modifications
        $show_instances_table = $schema->getTable('cc_show_instances');

        $show_instances_table->addColumn('record', 'smallint', array('notnull' => 0, 'default' => 0));
        $show_instances_table->addColumn('rebroadcast', 'smallint', array('notnull' => 0, 'default' => 0));
        $show_instances_table->addColumn('instance_id', 'integer',  array('notnull' => 0));
        $show_instances_table->addColumn('file_id', 'integer', array('notnull' => 0));
        $show_instances_table->addColumn('soundcloud_id', 'integer', array('notnull' => 0));

        $show_instances_table->addNamedForeignKeyConstraint('cc_original_show_instance_fkey', $show_instances_table, array('instance_id'), array('id'), array('onDelete' => 'CASCADE'));

        $files_table = $schema->getTable('cc_files');
        $show_instances_table->addNamedForeignKeyConstraint('cc_recorded_file_fkey', $files_table, array('file_id'), array('id'), array('onDelete' => 'CASCADE'));
        //end cc_show_instances modifications

        //start cc_show_days modifications
        $show_days_table = $schema->getTable('cc_show_days');

        $show_days_table->addColumn('record', 'smallint', array( 'notnull' => 0, 'default' => 0));
        //end cc_show_days modifications

        //start cc_show modifications
        $show_table = $schema->getTable('cc_show');

        $show_table->addColumn('url', 'string', array('notnull' => 0, 'length' => 255));
        //end cc_show modifications

        //start cc_schedule modifications
        $schedule_table = $schema->getTable('cc_schedule');

        $playlist_id_col = $schedule_table->getColumn('playlist_id');
        $playlist_id_col->setNotnull(false);
        //end cc_schedule modifications

        //create cc_show_rebroadcast table
        $cc_show_rebroadcast_table = $schema->createTable('cc_show_rebroadcast');

        $cc_show_rebroadcast_table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $cc_show_rebroadcast_table->addColumn('day_offset', 'string', array('length' => 255));
        $cc_show_rebroadcast_table->addColumn('start_time', 'datetime',  array('notnull' => 1));
        $cc_show_rebroadcast_table->addColumn('show_id', 'integer',  array('notnull' => 1));

        $cc_show_rebroadcast_table->setPrimaryKey(array('id'));
        //end create cc_show_rebroadcast table
    }

    public function down(Schema $schema)
    {
        //start cc_show_instances modifications
        $show_instances_table = $schema->getTable('cc_show_instances');

        $show_instances_table->dropColumn('record');
        $show_instances_table->dropColumn('rebroadcast');
        $show_instances_table->dropColumn('instance_id');
        $show_instances_table->dropColumn('file_id');
        $show_instances_table->dropColumn('soundcloud_id');
        //end cc_show_instances modifications

        //start cc_show_days modifications
        $show_days_table = $schema->getTable('cc_show_days');

        $show_days_table->dropColumn('record');
        //end cc_show_days modifications

        //start cc_show modifications
        $show_table = $schema->getTable('cc_show');

        $show_table->dropColumn('url');
        //end cc_show modifications

        //start cc_schedule modifications
        $schedule_table = $schema->getTable('cc_schedule');

        $playlist_id_col = $schedule_table->getColumn('playlist_id');
        $playlist_id_col->setNotnull(true);
        //end cc_schedule modifications

        //drop cc_show_rebroadcast table
        $schema->dropTable('cc_show_rebroadcast');
        //end drop cc_show_rebroadcast table
    }
}
