<?php

//CC-2279 Upgrade script for converting stor directory to new format

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110629143017 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //create cc_music_dirs table
        $cc_music_dirs = $schema->createTable('cc_music_dirs');

        $cc_music_dirs->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $cc_music_dirs->addColumn('type', 'string', array('length' => 255));
        $cc_music_dirs->addColumn('directory', 'text',  array('unique' => true));

        $cc_music_dirs->setPrimaryKey(array('id'));

        //end create cc_music_dirs table


        //start cc_files modifications
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('directory', 'integer', array('default'=> -1));

        //$cc_files->addNamedForeignKeyConstraint('cc_music_dirs_folder_fkey', $cc_music_dirs, array('directory'), array('id'), array('onDelete' => 'CASCADE'));
        //end cc_files modifications
    }

    public function down(Schema $schema)
    {

    }
}
