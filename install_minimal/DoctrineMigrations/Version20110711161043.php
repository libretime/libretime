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
        
        // before 3) we have to delete all entries in cc_schedule with file_id that are not in cc_file table
        $this->_addSql("DELETE FROM cc_schedule WHERE cc_schedule.id IN(
                        SELECT cc_schedule.id
                        FROM cc_schedule
                        LEFT JOIN cc_files
                        ON cc_schedule.file_id = cc_files.id
                        WHERE cc_files.id IS NULL)");
        
        /* 3) create a foreign key relationship from cc_schedule to cc_files */
        $cc_schedule = $schema->getTable('cc_schedule');
        $cc_schedule->addNamedForeignKeyConstraint('cc_files_folder_fkey', $cc_files, array('file_id'), array('id'), array('onDelete' => 'CASCADE'));
    }

    public function down(Schema $schema)
    {

    }
}
