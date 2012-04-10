<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120410115311 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->_addSql("UPDATE cc_files SET temp_br = bit_rate::integer");
        $this->_addSql("UPDATE cc_files SET temp_sr = sample_rate::integer");
    }

    public function down(Schema $schema)
    {

    }
}