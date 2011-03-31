<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema,
    Doctrine\DBAL\Schema\Column,
    Doctrine\DBAL\Types\Type;

class Version20110331111708 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //start cc_show_instances modifications
        $show_instances_table = $schema->getTable("cc_show_instances");

        $show_instances_table->addColumn('record', 'smallint', array( 'notnull' => 0, 'default' => 0));
        $show_instances_table->addColumn('rebroadcast', 'smallint', array( 'notnull' => 0, 'default' => 0));
        $show_instances_table->addColumn('instance_id', 'integer',  array( 'notnull' => 0));
        $show_instances_table->addColumn('file_id', 'integer', array( 'notnull' => 0));
        $show_instances_table->addColumn('soundcloud_id', 'integer', array( 'notnull' => 0));

        $show_instances_table->addNamedForeignKeyConstraint("cc_original_show_instance_fkey", $show_instances_table, array("instance_id"), array("id"), array("onDelete" => "CASCADE"));

        $files_table = $schema->getTable("cc_files");
        $show_instances_table->addNamedForeignKeyConstraint("cc_recorded_file_fkey", $files_table, array("file_id"), array("id"), array("onDelete" => "CASCADE"));
        //end cc_show_instances modifications

        //start cc_show_days modifications
        $show_days_table = $schema->getTable("cc_show_days");

        $show_days_table->addColumn('record', 'smallint', array( 'notnull' => 0, 'default' => 0));
        //end cc_show_days modifications
    }

    public function down(Schema $schema)
    {
        //start cc_show_instances modifications
        $show_instances_table = $schema->getTable("cc_show_instances");

        $show_instances_table->dropColumn("record");
        $show_instances_table->dropColumn("rebroadcast");
        $show_instances_table->dropColumn("instance_id");
        $show_instances_table->dropColumn("file_id");
        $show_instances_table->dropColumn("soundcloud_id");
        //end cc_show_instances modifications

        //start cc_show_days modifications
        $show_days_table = $schema->getTable("cc_show_days");

        $show_days_table->dropColumn("record");
        //end cc_show_days modifications
    }
}
