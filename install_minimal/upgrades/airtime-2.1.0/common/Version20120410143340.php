<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410143340 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //convert column creator to be creator_id on cc_playlist
        
        $this->_addSql("ALTER TABLE cc_playlist ADD creator_id integer");
        $this->_addSql("UPDATE cc_playlist SET creator_id = (SELECT id FROM cc_subjs WHERE creator = login)");
        $this->_addSql("ALTER TABLE cc_playlist DROP COLUMN creator");
        
        $this->_addSql("ALTER TABLE cc_playlist ADD CONSTRAINT cc_playlist_createdby_fkey FOREIGN KEY (creator_id) REFERENCES cc_subjs(id) NOT DEFERRABLE INITIALLY IMMEDIATE");

        $this->_addSql("ALTER TABLE cc_playlist ADD utime timestamp(6)");
        //set the initial created to modified time since this is the closest we can get to inital creation time.
        $this->_addSql("UPDATE cc_playlist SET utime = mtime");
        
        $this->_addSql("ALTER TABLE cc_playlist ADD length interval default '00:00:00'");
        //copy length property from our old view cc_playlisttimes
        $this->_addSql("UPDATE cc_playlist AS pl SET length = (SELECT pt.length FROM cc_playlisttimes AS pt WHERE pt.id = pl.id)");
        //drop the view as it is no longer needed.
        $this->_addSql("DROP VIEW cc_playlisttimes");
        
        $this->_addSql("ALTER TABLE cc_playlist DROP COLUMN state");
        $this->_addSql("ALTER TABLE cc_playlist DROP COLUMN currentlyaccessing");
        $this->_addSql("ALTER TABLE cc_playlist DROP COLUMN editedby");
    }

    public function down(Schema $schema)
    {

    }
}