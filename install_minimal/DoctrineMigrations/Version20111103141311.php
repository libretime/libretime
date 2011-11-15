<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20111103141311 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // add timezone column to cc_show_days
        $cc_subjs = $schema->getTable('cc_show_days');
        $cc_subjs->addColumn('timezone', 'string', array('required' => true, 'default'=> ''));
    }

    public function down(Schema $schema)
    {

    }
}
