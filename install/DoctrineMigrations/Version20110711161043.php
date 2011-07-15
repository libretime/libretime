<?php

namespace DoctrineMigrations;

/*
update cc_files table to include to "directory" column as well as add foreign key relation to
cc_music_dirs table.
*/

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110711161043 extends AbstractMigration
{
    public function up(Schema $schema)
    {
    
        //CREATE the default value of "/srv/airtime/stor", this can be updated later in the upgrade script.
        $this->_addSql("INSERT INTO cc_music_dirs (type, directory) VALUES ('stor', '/srv/airtime/stor');");
        
        $this->_addSql("INSERT INTO cc_music_dirs (type, directory) VALUES ('upgrade', '');");
    }
    
    public function postUp(Schema $schema){
        $cc_music_dirs = $schema->getTable('cc_music_dirs');
    
        //start cc_files modifications
        $cc_files = $schema->getTable('cc_files');
        $cc_files->addColumn('directory', 'integer', array('default'=> 2));

        $cc_files->addNamedForeignKeyConstraint('cc_music_dirs_folder_fkey', $cc_music_dirs, array('directory'), array('id'), array('onDelete' => 'CASCADE'));
        //end cc_files modifications
    }

    public function down(Schema $schema)
    {

    }
}
