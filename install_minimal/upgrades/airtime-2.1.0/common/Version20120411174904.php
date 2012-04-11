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
        $this->_addSql("ALTER TABLE cc_show_instances ADD created timestamp(6)");
        $this->_addSql("ALTER TABLE cc_show_instances ADD last_scheduled timestamp(6)");
    }

    public function down(Schema $schema)
    {

    }
}