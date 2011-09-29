<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20110929184401 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $cc_component = $schema->createTable('cc_component');
        $cc_component->addColumn('name', 'string', array('length' => 32));
        $cc_component->addColumn('ip', 'string', array('length' => 18));

        $cc_component->setPrimaryKey(array('name'));
    }

    public function down(Schema $schema)
    {

    }
}
