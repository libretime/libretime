<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120411174904 extends AbstractMigration
{
    /*
     * modifications to cc_show_instances for 2.1
     */
    public function up(Schema $schema)
    {
        $this->_addSql("ALTER TABLE cc_show_instances ADD created timestamp");
        $this->_addSql("ALTER TABLE cc_show_instances ADD last_scheduled timestamp");
        
        //setting these to a default now for timeline refresh purposes.
        $now = gmdate("Y-m-d H:i:s");
        $this->_addSql("UPDATE cc_show_instances SET created = '$now'");
        $this->_addSql("UPDATE cc_show_instances SET last_scheduled = '$now'");
        
        $this->_addSql("ALTER TABLE cc_show_instances ALTER COLUMN created SET NOT NULL");
    }

    public function down(Schema $schema)
    {

    }
}
