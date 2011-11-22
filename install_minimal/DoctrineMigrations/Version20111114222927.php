<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20111114222927 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $cc_show_instances = $schema->getTable('cc_show_instances');
        $cc_show_instances->addColumn('modified_instance', 'boolean', array('notnull' => true, 'default'=> '0'));
    }

    public function down(Schema $schema)
    {

    }
}
