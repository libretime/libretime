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
    }

    public function down(Schema $schema)
    {

    }
}