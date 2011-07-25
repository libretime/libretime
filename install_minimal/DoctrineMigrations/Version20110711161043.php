<?php

namespace DoctrineMigrations;

/*
1) update cc_files table to include to "directory" column
2) create a foreign key relationship from cc_files to cc_music_dirs
3) create a foreign key relationship from cc_schedule to cc_files
*/

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110711161043 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        /* 1) update cc_files table to include to "directory" column */
        $this->_addSql("INSERT INTO cc_music_dirs (type, directory) VALUES ('stor', '/srv/airtime/stor/');");

        $this->_addSql("INSERT INTO cc_music_dirs (type, directory) VALUES ('link', '');");

        $cc_music_dirs = $schema->getTable('cc_music_dirs');

        /* 2) create a foreign key relationship from cc_files to cc_music_dirs */
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('directory', 'integer', array('notnull' => 0, 'default'=> NULL));

        $cc_files->addNamedForeignKeyConstraint('cc_music_dirs_folder_fkey', $cc_music_dirs, array('directory'), array('id'), array('onDelete' => 'CASCADE'));

        /* 3) create a foreign key relationship from cc_schedule to cc_files */
        $cc_schedule = $schema->getTable('cc_schedule');
        $cc_schedule->addNamedForeignKeyConstraint('cc_files_folder_fkey', $cc_files, array('file_id'), array('id'), array('onDelete' => 'CASCADE'));
    }

    public function down(Schema $schema)
    {

    }
}
