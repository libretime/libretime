<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20120403143635 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $cc_show = $schema->getTable('cc_show');
        $cc_show->addColumn('live_stream_using_airtime_auth', 'boolean', array('default'=> 'false'));
        $cc_show->addColumn('live_stream_using_custom_auth', 'boolean', array('default'=> 'false'));
        $cc_show->addColumn('live_stream_user', 'string', array('notnull' => 0, 'length' => 255));
        $cc_show->addColumn('live_stream_pass', 'string', array('notnull' => 0, 'length' => 255));
    }

    public function down(Schema $schema)
    {

    }
}