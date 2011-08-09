<?php
namespace DoctrineMigrations;
//CC-2279 Upgrade script for creating the cc_music_dirs table.

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
    }
    


    public function down(Schema $schema)
    {
        $schema->dropTable('cc_music_dirs');
    }
}
