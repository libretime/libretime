<?php

namespace Airtime\MediaItem\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'media_playlist_rule' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.airtime.map
 */
class PlaylistRuleTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'airtime.map.PlaylistRuleTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('media_playlist_rule');
        $this->setPhpName('PlaylistRule');
        $this->setClassname('Airtime\\MediaItem\\PlaylistRule');
        $this->setPackage('airtime');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('media_playlist_rule_id_seq');
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('criteria', 'Criteria', 'VARCHAR', true, 32, null);
        $this->addColumn('modifier', 'Modifier', 'VARCHAR', true, 16, null);
        $this->addColumn('value', 'Value', 'VARCHAR', true, 512, null);
        $this->addColumn('extra', 'Extra', 'VARCHAR', false, 512, null);
        $this->addForeignKey('media_id', 'MediaId', 'INTEGER', 'media_item', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('MediaItem', 'Airtime\\MediaItem', RelationMap::MANY_TO_ONE, array('media_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

} // PlaylistRuleTableMap
