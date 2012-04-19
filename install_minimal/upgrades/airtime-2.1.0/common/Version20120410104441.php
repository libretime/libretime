<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410104441 extends AbstractMigration
{
    /*
     * contains modifications to cc_files for 2.1
     */
    public function up(Schema $schema)
    {
        $this->_addSql("DROP TRIGGER calculate_position ON cc_playlistcontents");
        $this->_addSql("DROP FUNCTION calculate_position()");
        
        $this->_addSql("ALTER TABLE cc_subjs_token ALTER COLUMN created TYPE timestamp");
        
        $this->_addSql("ALTER TABLE cc_subjs_token ADD CONSTRAINT cc_subjs_token_idx UNIQUE (token);");
        $this->_addSql("ALTER TABLE cc_subjs_token ADD CONSTRAINT cc_subjs_token_userid_fkey FOREIGN KEY (user_id) REFERENCES cc_subjs(id) ON DELETE CASCADE");
        
        //add temp columns for changing bitrate and sample rate to integers.
        $this->_addSql("ALTER TABLE cc_files ADD temp_br integer");
        $this->_addSql("ALTER TABLE cc_files ADD temp_sr integer");
        
        $this->_addSql("UPDATE cc_files SET temp_br = bit_rate::integer");
        $this->_addSql("UPDATE cc_files SET temp_sr = sample_rate::integer");
        
        $this->_addSql("ALTER TABLE cc_files DROP COLUMN sample_rate");
        $this->_addSql("ALTER TABLE cc_files DROP COLUMN bit_rate");
        
        $this->_addSql("ALTER TABLE cc_files RENAME COLUMN temp_sr TO sample_rate");
        $this->_addSql("ALTER TABLE cc_files RENAME COLUMN temp_br TO bit_rate");
        
        //add utime, lptime
        $this->_addSql("ALTER TABLE cc_files ADD utime timestamp");
        $this->_addSql("ALTER TABLE cc_files ADD lptime timestamp");
        
        //setting these to a default now for timeline refresh purposes.
        $now = gmdate("Y-m-d H:i:s");
        $this->_addSql("UPDATE cc_files SET utime = '$now'");
    }
    
    public function down(Schema $schema)
    {

    }
}