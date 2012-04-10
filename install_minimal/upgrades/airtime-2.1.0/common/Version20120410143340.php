<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410143340 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //add temp columns for changing bitrate and sample rate to integers.
        $this->_addSql("ALTER TABLE cc_playlist ADD creator_id integer");
        
        $this->_addSql("UPDATE cc_playlist SET creator_id = ()");
    }

    public function down(Schema $schema)
    {

    }
}