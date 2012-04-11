<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120411102907 extends AbstractMigration
{
    /*
     * changing many columns from time without timezone to interval
     * 
     * altering cc_schedule for 2.1
     */
    public function up(Schema $schema)
    {
        $this->_addSql("ALTER TABLE cc_files ALTER COLUMN length TYPE interval");
        
        $this->_addSql("ALTER TABLE cc_playlistcontents ALTER COLUMN cuein TYPE interval");
        $this->_addSql("ALTER TABLE cc_playlistcontents ALTER COLUMN cueout TYPE interval");
        $this->_addSql("ALTER TABLE cc_playlistcontents ALTER COLUMN cliplength TYPE interval");
        
        $this->_addSql("ALTER TABLE cc_schedule ALTER COLUMN cue_in TYPE interval");
        $this->_addSql("ALTER TABLE cc_schedule ALTER COLUMN cue_out TYPE interval");
        $this->_addSql("ALTER TABLE cc_schedule ALTER COLUMN clip_length TYPE interval");
        
        //remove old columns from cc_schedule that deal with groups or playlists.
        $this->_addSql("ALTER TABLE cc_schedule DROP COLUMN group_id");
        $this->_addSql("ALTER TABLE cc_schedule DROP COLUMN schedule_group_played");
        $this->_addSql("ALTER TABLE cc_schedule DROP COLUMN playlist_id");
        
        $this->_addSql("ALTER TABLE cc_schedule ADD playout_status integer DEFAULT 1 NOT NULL");
    }

    public function down(Schema $schema)
    {

    }
}