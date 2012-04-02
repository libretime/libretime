<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120402103944 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $cc_music_dirs = $schema->getTable('cc_music_dirs');
        $cc_music_dirs->addColumn('watched', 'boolean', array('default'=> 'true'));
        $cc_music_dirs->addColumn('exists', 'boolean', array('default'=> 'true'));
        
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('file_exists', 'boolean', array('default'=> 'true'));
        
        $this->dropForeignKey('cc_files', 'cc_music_dirs_folder_fkey');
        $cc_files->addNamedForeignKeyConstraint('cc_music_dirs_folder_fkey', $cc_music_dirs, array('directory'), array('id'));
        $cc_files->addIndex('file_exists', 'cc_files_file_exists_idx');
    }

    public function down(Schema $schema)
    {

    }
}